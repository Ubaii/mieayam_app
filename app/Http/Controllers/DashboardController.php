<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now();
        $todayTransactions = Transaction::query()
            ->where('status', 'completed')
            ->whereDate('paid_at', $today->toDateString());
        $todayTransactionCount = (clone $todayTransactions)->count();
        $todayRevenue = (clone $todayTransactions)->sum('total');
        $todayItems = TransactionItem::query()
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereDate('paid_at', $today->toDateString()))
            ->sum('quantity');

        $week = collect(range(6, 0))->map(function (int $daysAgo) use ($today): array {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->toDateString(),
                'revenue' => (int) Transaction::query()
                    ->where('status', 'completed')
                    ->whereDate('paid_at', $date->toDateString())
                    ->sum('total'),
            ];
        });
        $weeklyMax = max(1, (int) $week->max('revenue'));
        $bestSellers = TransactionItem::query()
            ->select('name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(price * quantity) as revenue'))
            ->whereHas('transaction', fn ($query) => $query->where('status', 'completed')->whereDate('paid_at', $today->toDateString()))
            ->groupBy('name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();
        $recentTransactions = Transaction::query()
            ->with(['cashier', 'items'])
            ->latest('paid_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'todayTransactionCount' => $todayTransactionCount,
            'todayRevenue' => $todayRevenue,
            'todayItems' => $todayItems,
            'availableMenus' => Menu::query()->where('is_active', true)->whereHas('category', fn ($query) => $query->where('is_active', true))->count(),
            'totalMenus' => Menu::query()->count(),
            'week' => $week,
            'weeklyMax' => $weeklyMax,
            'bestSellers' => $bestSellers,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
