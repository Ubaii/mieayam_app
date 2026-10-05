<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CafeTableController extends Controller
{
    private const STATUSES = ['available', 'occupied', 'reserved', 'maintenance'];

    public function index(Request $request): View
    {
        $tables = CafeTable::query()
            ->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request): void {
                $term = '%'.$request->string('search')->toString().'%';
                $query->where('table_number', 'like', $term)
                    ->orWhere('location', 'like', $term);
            }))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('table_number')
            ->get();

        return view('tables.index', compact('tables'));
    }

    public function status(): View
    {
        $tables = CafeTable::query()->where('is_active', true)->orderBy('table_number')->get();

        return view('tables.status', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'table_number' => ['required', 'string', 'max:30', 'unique:cafe_tables,table_number'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);

        CafeTable::create($attributes);

        return back()->with('status', 'Meja berhasil ditambahkan.');
    }

    public function edit(CafeTable $table): View
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, CafeTable $table): RedirectResponse
    {
        $attributes = $request->validate([
            'table_number' => ['required', 'string', 'max:30', Rule::unique('cafe_tables')->ignore($table)],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'location' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'is_active' => ['required', 'boolean'],
        ]);

        $table->update($attributes);

        return redirect()->route('tables.index')->with('status', 'Meja berhasil diperbarui.');
    }

    public function toggleActive(CafeTable $table): RedirectResponse
    {
        $table->update(['is_active' => ! $table->is_active]);

        return back()->with('status', 'Status aktif meja berhasil diperbarui.');
    }

    public function destroy(CafeTable $table): RedirectResponse
    {
        $table->delete();

        return back()->with('status', 'Meja berhasil dihapus.');
    }
}
