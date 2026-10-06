@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Ringkasan aktivitas MIE AYAM WENGI'57 hari ini.</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline"><x-icon name="calendar" /> Hari ini</a>
    </div>

    <section class="stat-grid" aria-label="Ringkasan hari ini">
        <x-stat-card label="Transaksi Hari Ini" :value="$todayTransactionCount" icon="receipt" tone="blue" :note="$todayTransactionCount ? 'Transaksi lunas hari ini' : 'Belum ada transaksi'" />
        <x-stat-card label="Pendapatan Hari Ini" :value="'Rp ' . number_format($todayRevenue, 0, ',', '.')" icon="chart" tone="green"
            note="Dari transaksi yang berhasil" />
        <x-stat-card label="Item Terjual" :value="$todayItems" icon="bag" tone="amber" note="Dari transaksi hari ini" />
        <x-stat-card label="Menu Tersedia" :value="$availableMenus . ' / ' . $totalMenus" icon="coffee" tone="violet" note="Menu aktif" />
    </section>

    <section class="dashboard-grid">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <h2>Pendapatan Mingguan</h2>
                    <p>Ringkasan pendapatan 7 hari terakhir.</p>
                </div>
                <span class="badge badge-neutral">Minggu ini</span>
            </div>
            @if ($week->sum('revenue'))
                <div class="weekly-bars">
                    @foreach ($week as $day)
                        <div class="weekly-bar-column"
                            title="{{ $day['label'] }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }}">
                            <strong>Rp {{ number_format($day['revenue'], 0, ',', '.') }}</strong>
                            <span class="weekly-bar-track"><i
                                    style="height:{{ max(3, round(($day['revenue'] / $weeklyMax) * 100)) }}%"></i></span>
                            <small>{{ $day['label'] }}</small>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="chart-wrap">
                    <p class="empty-state">Belum ada data pendapatan untuk ditampilkan.</p>
                </div>
            @endif
        </article>
        <article class="panel">
            <div class="panel-header">
                <div>
                    <h2>Menu Terlaris Hari Ini</h2>
                    <p>Paling banyak dipesan pelanggan</p>
                </div><a href="{{ route('reports.index') }}" class="text-link">Lihat laporan</a>
            </div>
            <div class="top-menu-list">
                @forelse($bestSellers as $item)
                    <div class="rank-row"><span class="rank-number">{{ $loop->iteration }}</span><span
                            class="rank-copy"><strong>{{ $item->name }}</strong><small>{{ $item->quantity }}
                                terjual</small></span><span class="rank-value">Rp
                            {{ number_format($item->revenue, 0, ',', '.') }}</span></div>
                @empty
                    <p class="empty-state">Menu terlaris akan muncul setelah transaksi tercatat.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="panel" style="margin-bottom:16px">
        <div class="panel-header">
            <div>
                <h2>Transaksi Terbaru</h2>
                <p>Aktivitas transaksi terakhir di toko</p>
            </div><a href="{{ route('transactions.index') }}" class="text-link">Lihat semua</a>
        </div>
        <div class="transaction-list">
            @forelse($recentTransactions as $transaction)
                <a href="{{ route('transactions.receipt', $transaction) }}" class="transaction-row">
                    <span class="transaction-main"><span class="transaction-symbol"><x-icon
                                name="receipt" /></span><span><strong>{{ $transaction->invoice }}</strong><small>{{ $transaction->paid_at->translatedFormat('d M Y, H:i') }}
                                · {{ $transaction->cashier->name }}</small></span></span>
                    <span class="transaction-amount">Rp
                        {{ number_format($transaction->total, 0, ',', '.') }}<small>{{ $transaction->items->sum('quantity') }}
                            item</small></span>
                </a>
            @empty
                <p class="empty-state">Transaksi terbaru akan muncul di sini.</p>
            @endforelse
        </div>
    </section>

    <section class="quick-actions" aria-label="Aksi cepat">
        <a class="quick-action" href="{{ route('cashier') }}"><x-icon name="cashier" /> Mulai Kasir</a>
        <a class="quick-action" href="{{ route('menus.index') }}"><x-icon name="coffee" /> Kelola Menu</a>
        <a class="quick-action" href="{{ route('reports.index') }}"><x-icon name="chart" /> Lihat Laporan</a>
    </section>
@endsection
