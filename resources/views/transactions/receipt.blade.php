@extends('layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
    @if($transaction)
        <div class="page-heading"><div><h1>Detail Transaksi</h1><p>Periksa detail pesanan atau cetak struk pelanggan.</p></div><a href="{{ route(auth()->user()->isAdmin() ? 'transactions.index' : 'cashier') }}" class="btn btn-outline">{{ auth()->user()->isAdmin() ? 'Kembali ke transaksi' : 'Kembali ke kasir' }}</a></div>
        <div class="receipt-wrap">
            <article class="receipt-paper">
                <header class="receipt-brand"><span class="brand-mark">M</span><h2>MIE AYAM WENGI'57</h2><p>Restaurant Management System</p></header>
                <div class="receipt-meta">
                    <span>No. Invoice</span><strong>{{ $transaction->invoice }}</strong>
                    <span>Tanggal</span><strong>{{ $transaction->paid_at->translatedFormat('d F Y, H:i') }}</strong>
                    <span>Kasir</span><strong>{{ $transaction->cashier->name }}</strong>
                    <span>Meja</span><strong>{{ $transaction->cafeTable?->table_number ?? 'Bawa pulang' }}</strong>
                    <span>Metode bayar</span><strong>{{ $transaction->payment_method }}</strong>
                </div>
                <div class="receipt-items">
                    @foreach($transaction->items as $item)
                        <div class="receipt-item"><div><strong>{{ $item->name }}</strong><small>{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}@if($item->note) · {{ $item->note }}@endif</small></div><b>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</b></div>
                    @endforeach
                </div>
                <div class="receipt-totals">
                    <div class="receipt-total-row"><span>Subtotal</span><span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>
                    <div class="receipt-total-row total"><span>Total</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
                    <div class="receipt-total-row"><span>Pembayaran</span><span>Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span></div>
                    <div class="receipt-total-row"><span>Kembalian</span><span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>
                </div>
                @if($transaction->note)<p class="receipt-note"><strong>Catatan transaksi:</strong> {{ $transaction->note }}</p>@endif
                <footer class="receipt-thanks">Terima kasih telah berkunjung ke MIE AYAM WENGI'57.<br>Semoga hari Anda sehangat mie kami.</footer>
            </article>
        </div>
        <div class="print-actions"><button type="button" class="btn btn-primary" onclick="window.print()"><x-icon name="receipt" /> Cetak struk</button><a href="{{ route('cashier') }}" class="btn btn-outline">Transaksi baru</a></div>
    @else
        <div class="page-heading"><div><h1>Struk Transaksi</h1><p>Struk akan tersedia setelah pembayaran pertama selesai.</p></div><a href="{{ route(auth()->user()->isAdmin() ? 'transactions.index' : 'cashier') }}" class="btn btn-outline">{{ auth()->user()->isAdmin() ? 'Kembali ke transaksi' : 'Kembali ke kasir' }}</a></div>
        <section class="panel"><p class="empty-state">Belum ada transaksi untuk ditampilkan. Selesaikan pembayaran melalui kasir untuk membuat struk.</p></section>
    @endif
@endsection
