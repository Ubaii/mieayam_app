<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($filters['from'] ?? now()->toDateString())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->toDateString())->endOfDay();

        $transactions = Transaction::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$from, $to]);
        $count = (clone $transactions)->count();
        $revenue = (int) (clone $transactions)->sum('total');
        $itemsSold = TransactionItem::query()
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereBetween('paid_at', [$from, $to]))
            ->sum('quantity');

        $days = min(14, max(1, (int) $from->diffInDays($to) + 1));
        $sales = collect(range(0, $days - 1))->map(function (int $offset) use ($from): array {
            $date = $from->copy()->addDays($offset);

            return [
                'label' => $date->translatedFormat('d M'),
                'revenue' => (int) Transaction::query()
                    ->where('status', 'completed')
                    ->whereDate('paid_at', $date->toDateString())
                    ->sum('total'),
            ];
        });
        $salesMax = max(1, (int) $sales->max('revenue'));
        $paymentMethods = (clone $transactions)
            ->select('payment_method', DB::raw('COUNT(*) as transactions_count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();
        $bestSellers = TransactionItem::query()
            ->select('name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(price * quantity) as revenue'))
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereBetween('paid_at', [$from, $to]))
            ->groupBy('name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'filters',
            'from',
            'to',
            'count',
            'revenue',
            'itemsSold',
            'sales',
            'salesMax',
            'paymentMethods',
            'bestSellers',
        ) + ['average' => $count ? (int) round($revenue / $count) : 0]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($filters['from'] ?? now()->toDateString())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->toDateString())->endOfDay();

        $transactions = Transaction::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$from, $to]);
        $count = (clone $transactions)->count();
        $revenue = (int) (clone $transactions)->sum('total');
        $itemsSold = TransactionItem::query()
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereBetween('paid_at', [$from, $to]))
            ->sum('quantity');

        $days = min(14, max(1, (int) $from->diffInDays($to) + 1));
        $sales = collect(range(0, $days - 1))->map(function (int $offset) use ($from): array {
            $date = $from->copy()->addDays($offset);

            return [
                'label' => $date->translatedFormat('d M'),
                'revenue' => (int) Transaction::query()
                    ->where('status', 'completed')
                    ->whereDate('paid_at', $date->toDateString())
                    ->sum('total'),
            ];
        });
        $paymentMethods = (clone $transactions)
            ->select('payment_method', DB::raw('COUNT(*) as transactions_count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();
        $bestSellers = TransactionItem::query()
            ->select('name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(price * quantity) as revenue'))
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereBetween('paid_at', [$from, $to]))
            ->groupBy('name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        $average = $count ? (int) round($revenue / $count) : 0;

        $filename = 'laporan-' . $from->format('d-m-Y') . '-sampai-' . $to->format('d-m-Y') . '.pdf';

        $pdf = Pdf::loadView('reports.pdf', compact(
            'from',
            'to',
            'count',
            'revenue',
            'itemsSold',
            'sales',
            'paymentMethods',
            'bestSellers',
            'average',
        ));

        return $pdf->download($filename);
    }
}
