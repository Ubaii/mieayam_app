@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="page-heading">
        <div><h1>Laporan Penjualan</h1><p>Ringkasan performa penjualan MIE AYAM WENGI'57.</p></div>
        <div class="heading-actions">
            <form class="flex items-center gap-3" method="get" action="{{ route('reports.index') }}">
                <label class="sr-only" for="report-from">Dari</label>
                <input class="input-control" style="width:auto" id="report-from" name="from" type="date" value="{{ $from->toDateString() }}" required>
                <label class="sr-only" for="report-to">Sampai</label>
                <input class="input-control" style="width:auto" id="report-to" name="to" type="date" value="{{ $to->toDateString() }}" required>
                <button class="btn btn-outline" type="submit"><x-icon name="calendar" /> Terapkan</button>
            </form>
            <a href="{{ route('reports.pdf', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-primary"><x-icon name="download" /> Download PDF</a>
        </div>
    </div>
    <section class="stat-grid">
        <x-stat-card label="Total Transaksi" :value="$count" icon="receipt" tone="blue" note="Pada periode terpilih" />
        <x-stat-card label="Total Pendapatan" :value="'Rp '.number_format($revenue, 0, ',', '.')" icon="chart" tone="green" note="Transaksi selesai" />
        <x-stat-card label="Item Terjual" :value="$itemsSold" icon="bag" tone="amber" note="Pada periode terpilih" />
        <x-stat-card label="Rata-rata Transaksi" :value="'Rp '.number_format($average, 0, ',', '.')" icon="wallet" tone="violet" note="Per transaksi selesai" />
    </section>
    <section class="panel" style="margin-bottom:16px">
        <div class="panel-header"><div><h2>Grafik Penjualan</h2><p>{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</p></div><span class="badge badge-neutral">Rp {{ number_format($revenue, 0, ',', '.') }}</span></div>
        @if($revenue)
            <div class="weekly-bars">
                @foreach($sales as $day)
                    <div class="weekly-bar-column" title="{{ $day['label'] }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }}">
                        <strong>Rp {{ number_format($day['revenue'], 0, ',', '.') }}</strong>
                        <span class="weekly-bar-track"><i style="height: {{ max(3, round($day['revenue'] / $salesMax * 100)) }}%"></i></span>
                        <small>{{ $day['label'] }}</small>
                    </div>
                @endforeach
            </div>
        @else
            <div class="chart-wrap"><p class="empty-state">Belum ada data penjualan untuk periode ini.</p></div>
        @endif
    </section>
    <section class="report-grid">
        <article class="panel">
            <div class="panel-header"><div><h2>Pendapatan per Metode Bayar</h2><p>Komposisi pembayaran periode ini</p></div></div>
            @forelse($paymentMethods as $method)
                <div class="report-method-row"><div><strong>{{ $method->payment_method }}</strong><small>{{ $method->transactions_count }} transaksi</small></div><b>Rp {{ number_format($method->revenue, 0, ',', '.') }}</b></div>
            @empty
                <p class="empty-state">Ringkasan metode pembayaran akan muncul setelah transaksi tersedia.</p>
            @endforelse
        </article>
        <article class="panel">
            <div class="panel-header"><div><h2>Menu Terlaris</h2><p>Berdasarkan jumlah item terjual</p></div></div>
            @forelse($bestSellers as $menu)
                <div class="bar-row"><span>{{ $menu->name }}</span><div class="bar-track"><span style="width:{{ max(3, round($menu->quantity / max(1, (int) $bestSellers->max('quantity')) * 100)) }}%"></span></div><b>{{ $menu->quantity }} terjual</b></div>
            @empty
                <p class="empty-state">Menu terlaris akan muncul setelah transaksi tersedia.</p>
            @endforelse
        </article>
    </section>
@endsection
