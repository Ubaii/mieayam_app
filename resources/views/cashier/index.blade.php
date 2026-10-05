@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <div class="page-heading">
        <div><h1>Kasir</h1><p>Pilih menu, atur pesanan, dan proses pembayaran.</p></div>
        <span class="store-status"><span></span> Kasir siap melayani</span>
    </div>

    <div class="pos-layout">
        <section class="pos-menu-panel">
            <div class="pos-filters">
                <div class="search-control"><x-icon name="search" /><input class="input-control" type="search" placeholder="Cari menu..." aria-label="Cari menu" data-menu-search></div>
                <select class="select-control" aria-label="Filter kategori" data-category-select>
                    <option value="">Semua Kategori</option>@foreach($categories as $category)<option value="{{ $category->name }}">{{ $category->name }}</option>@endforeach
                </select>
                <select class="select-control" aria-label="Tipe pesanan" data-cafe-table-picker>
                    <option value="">Bawa pulang</option>
                    <option value="dine-in">Makan di tempat</option>
                </select>
            </div>
            <div class="category-chips">
                <button type="button" class="category-chip active" data-category-chip="">Semua</button>
                @foreach($categories as $category)<button type="button" class="category-chip" data-category-chip="{{ $category->name }}">{{ $category->name }}</button>@endforeach
            </div>
            <div class="product-grid" data-product-grid>
                @foreach($menus as $menu)
                    <x-menu-card :id="$menu->id" :name="$menu->name" :category="$menu->category->name" :price="$menu->price" :tone="str_contains(strtolower($menu->name), 'matcha') ? 'matcha' : (str_contains(strtolower($menu->category->name), 'dessert') ? 'dessert' : (str_contains(strtolower($menu->category->name), 'food') ? 'food' : (str_contains(strtolower($menu->category->name), 'tea') ? 'tea' : 'coffee')))" />
                @endforeach
            </div>
            <p class="empty-state" data-products-empty @if($menus->isNotEmpty()) hidden @endif>{{ $menus->isEmpty() ? 'Belum ada menu aktif. Tambahkan dan aktifkan menu terlebih dahulu sebelum membuat pesanan.' : 'Menu tidak ditemukan. Coba kata kunci lain.' }}</p>
        </section>

        <aside class="order-panel">
            <header class="order-header">
                <div class="order-header-row"><h2>Pesanan</h2><button class="btn btn-quiet" type="button" data-clear-order> Kosongkan</button></div>
                <select class="order-table-select" aria-label="Tipe pesanan" data-order-table-display>
                    <option value="">Bawa pulang</option>
                    <option value="dine-in">Makan di tempat</option>
                </select>
            </header>
            <div class="order-items" data-order-items>
                <div class="order-empty" data-order-empty>Belum ada menu. Pilih menu di sebelah kiri untuk mulai membuat pesanan.</div>
            </div>
            <footer class="order-footer">
                <div class="order-total-row"><span>Total</span><span data-order-total>Rp 0</span></div>
                <button type="button" class="btn btn-primary" data-payment-open disabled>Bayar</button>
            </footer>
        </aside>
    </div>

    <x-modal id="payment-modal" title="Pembayaran">
        <div class="payment-total"><span>Total pembayaran</span><strong data-payment-total>Rp 0</strong></div>
        <form class="payment-form" data-payment-form action="{{ route('cashier.checkout') }}" method="post">
            @csrf
            <input type="hidden" name="table_id" value="" data-payment-table>
            <div data-payment-items></div>
            <div class="form-group">
                <span class="field-label">Metode pembayaran</span>
                <div class="payment-methods">
                    <label class="payment-option"><input type="radio" name="payment_method" value="Tunai" checked required><span>Tunai</span></label>
                    <label class="payment-option"><input type="radio" name="payment_method" value="QRIS" required><span>QRIS</span></label>
                </div>
            </div>
            <div class="form-group" data-tunai-fields>
                <label class="field-label" for="amount-paid">Uang yang dibayar</label>
                <input class="input-control" id="amount-paid" name="amount_paid" type="number" min="0" value="0" data-payment-amount required>
            </div>
            <div class="payment-change" data-change-display><span>Kembalian</span><strong data-payment-change>Rp 0</strong></div>
            <div class="form-group"><label class="field-label" for="payment-note">Catatan transaksi</label><textarea class="textarea-control" id="payment-note" name="note" placeholder="Catatan tambahan (opsional)"></textarea></div>
            <div class="modal-actions"><button type="button" class="btn btn-outline" data-modal-close>Batal</button><button type="submit" class="btn btn-primary">Proses Pembayaran</button></div>
        </form>
    </x-modal>
@endsection
