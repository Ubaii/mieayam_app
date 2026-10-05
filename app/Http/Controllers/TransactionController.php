<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function cashier(): View
    {
        return view('cashier.index', [
            'categories' => \App\Models\Category::query()
                ->where('is_active', true)
                ->whereHas('menus', fn ($query) => $query->where('is_active', true))
                ->orderBy('name')
                ->get(),
            'menus' => Menu::query()
                ->with('category')
                ->where('is_active', true)
                ->whereHas('category', fn ($query) => $query->where('is_active', true))
                ->orderBy('name')
                ->get(),
            'tables' => CafeTable::query()
                ->where('is_active', true)
                ->where('status', 'available')
                ->orderBy('table_number')
                ->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'method' => ['nullable', Rule::in(['Tunai', 'QRIS', 'Debit', 'Transfer'])],
            'status' => ['nullable', Rule::in(['completed'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $transactions = Transaction::query()
            ->with('cashier')
            ->when($request->filled('search'), fn ($query) => $query->where('invoice', 'like', '%'.$request->string('search')->toString().'%'))
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method', $request->input('method')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('paid_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('paid_at', '<=', $request->input('to')))
            ->latest('paid_at')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'table_id' => ['nullable', 'integer', Rule::exists('cafe_tables', 'id')->where('is_active', true)->where('status', 'available')],
            'payment_method' => ['required', Rule::in(['Tunai', 'QRIS', 'Debit', 'Transfer'])],
            'amount_paid' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.menu_id' => ['required', 'integer', 'distinct', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction = DB::transaction(function () use ($attributes, $request): Transaction {
            $menus = Menu::query()
                ->with('category')
                ->whereIn('id', collect($attributes['items'])->pluck('menu_id'))
                ->where('is_active', true)
                ->whereHas('category', fn ($query) => $query->where('is_active', true))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($menus->count() !== count($attributes['items'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => 'Satu atau lebih menu sudah tidak aktif. Muat ulang halaman kasir.',
                ]);
            }

            $subtotal = collect($attributes['items'])->sum(
                fn (array $item): int => $menus[$item['menu_id']]->price * $item['quantity'],
            );

            if ($subtotal > 1000000000) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => 'Total pesanan melebihi batas transaksi. Kurangi jumlah item.',
                ]);
            }

            if ($attributes['payment_method'] === 'Tunai' && $attributes['amount_paid'] < $subtotal) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount_paid' => 'Jumlah pembayaran belum mencukupi total pesanan.',
                ]);
            }
            $amountPaid = $attributes['payment_method'] === 'Tunai'
                ? $attributes['amount_paid']
                : $subtotal;

            $table = null;
            if (! empty($attributes['table_id'])) {
                $table = CafeTable::query()->lockForUpdate()->find($attributes['table_id']);
                if (! $table || ! $table->is_active || $table->status !== 'available') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'table_id' => 'Meja sudah tidak tersedia. Pilih meja lain atau pesanan bawa pulang.',
                    ]);
                }
            }

            do {
                $invoice = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            } while (Transaction::query()->where('invoice', $invoice)->exists());

            $transaction = Transaction::create([
                'invoice' => $invoice,
                'user_id' => $request->user()->id,
                'cafe_table_id' => $table?->id,
                'payment_method' => $attributes['payment_method'],
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'amount_paid' => $amountPaid,
                'change_amount' => $attributes['payment_method'] === 'Tunai' ? $amountPaid - $subtotal : 0,
                'note' => $attributes['note'] ?? null,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            foreach ($attributes['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $transaction->items()->create([
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                ]);
            }

            $table?->update(['status' => 'occupied']);

            return $transaction;
        });

        return redirect()->route('transactions.receipt', $transaction)->with('status', 'Pembayaran berhasil diproses.');
    }

    public function receipt(Request $request, Transaction $transaction): View
    {
        abort_unless(
            $request->user()->isAdmin() || $transaction->user_id === $request->user()->id,
            403,
        );

        $transaction->load(['items', 'cashier', 'cafeTable']);

        return view('transactions.receipt', compact('transaction'));
    }

    public function updateTableStatus(Request $request, CafeTable $table): RedirectResponse
    {
        $attributes = $request->validate([
            'status' => ['required', Rule::in(['available', 'occupied', 'reserved', 'maintenance'])],
        ]);

        $table->update($attributes);

        return back()->with('status', 'Status meja berhasil diperbarui.');
    }
}
