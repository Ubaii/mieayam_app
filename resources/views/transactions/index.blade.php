@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Riwayat Transaksi</h1>
            <p>Telusuri transaksi dan buka struk untuk detail pesanan.</p>
        </div>
        <div class="heading-actions">
            <a class="btn btn-outline" href="{{ route('reports.index') }}"><x-icon name="chart" /> Laporan</a>
        </div>
    </div>
    <section class="panel">
        <form method="get" action="{{ route('transactions.index') }}">
            <div class="filter-bar">
                <div class="filter-field search-control"><x-icon name="search" /><input class="input-control" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor invoice..."></div>
                <div class="filter-field"><label class="sr-only" for="method-filter">Metode pembayaran</label><select id="method-filter" class="select-control" name="method"><option value="">Semua metode</option>@foreach(['Tunai','QRIS','Debit','Transfer'] as $method)<option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>@endforeach</select></div>
                <div class="filter-field"><label class="sr-only" for="transaction-status-filter">Status transaksi</label><select id="transaction-status-filter" class="select-control" name="status"><option value="">Semua status</option><option value="completed" @selected(request('status') === 'completed')>Selesai</option></select></div>
            </div>
            <div class="filter-bar">
                <div class="filter-field"><label for="from-date">Dari tanggal</label><input class="input-control" id="from-date" name="from" type="date" value="{{ request('from') }}"></div>
                <div class="filter-field"><label for="to-date">Sampai tanggal</label><input class="input-control" id="to-date" name="to" type="date" value="{{ request('to') }}"></div>
                <button class="btn btn-outline" type="submit"><x-icon name="calendar" /> Terapkan filter</button>
                <a class="btn btn-quiet" href="{{ route('transactions.index') }}">Reset</a>
            </div>
        </form>
        <x-table>
            <thead><tr><th>NO. INVOICE</th><th>TANGGAL</th><th>TOTAL</th><th>METODE</th><th>STATUS</th><th>KASIR</th><th>AKSI</th></tr></thead>
            <tbody id="transaction-table">
                @forelse($transactions as $transaction)
                    <tr>
                        <td><span class="table-primary">{{ $transaction->invoice }}</span></td>
                        <td>{{ $transaction->paid_at->translatedFormat('d M Y H:i') }}</td>
                        <td><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                        <td><span class="badge badge-neutral">{{ $transaction->payment_method }}</span></td>
                        <td><span class="badge badge-success">Selesai</span></td>
                        <td>{{ $transaction->cashier->name }}</td>
                        <td><a class="table-action" href="{{ route('transactions.receipt', $transaction) }}" aria-label="Lihat struk {{ $transaction->invoice }}"><x-icon name="eye" /></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">Belum ada transaksi yang sesuai dengan filter.</td></tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="pagination-row"><span>Menampilkan {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi</span><div class="pagination-buttons">{{ $transactions->links() }}</div></div>
    </section>
@endsection
