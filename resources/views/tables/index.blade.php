@extends('layouts.app')

@section('title', 'Manajemen Meja')

@section('content')
    <div class="page-heading">
        <div><h1>Manajemen Meja</h1><p>Atur kapasitas, lokasi, dan status setiap meja.</p></div>
        <button type="button" class="btn btn-primary" data-modal-open="add-table"><x-icon name="plus" /> Tambah Meja</button>
    </div>
    <div class="filter-bar">
        <div class="filter-field search-control"><x-icon name="search" /><input class="input-control" type="search" placeholder="Cari nomor atau lokasi meja..." data-card-search=".table-management-card"></div>
        <div class="filter-field"><select class="select-control" aria-label="Filter status meja" data-status-filter><option value="">Semua status</option><option value="available">Tersedia</option><option value="occupied">Terisi</option><option value="reserved">Reserved</option><option value="maintenance">Maintenance</option></select></div>
        <a href="{{ route('tables.status') }}" class="btn btn-outline"><x-icon name="eye" /> Lihat status meja</a>
    </div>
    <section class="table-card-grid">
        @php($statuses = ['available' => ['Tersedia', 'badge-success'], 'occupied' => ['Terisi', 'badge-danger'], 'reserved' => ['Reserved', 'badge-warning'], 'maintenance' => ['Maintenance', 'badge-neutral']])
        @forelse($tables as $table)
            <article class="table-management-card" data-status="{{ $table->status }}">
                <div class="table-management-top"><strong>{{ $table->table_number }}</strong><span class="badge {{ $statuses[$table->status][1] }}">{{ $statuses[$table->status][0] }}</span></div>
                <p class="table-meta">{{ $table->capacity }} tempat duduk<small>{{ $table->location ?: 'Lokasi belum diatur' }}</small></p>
                <div class="table-management-footer">
                    <form action="{{ route('tables.active.toggle', $table) }}" method="post" class="inline-form" data-confirm="{{ $table->is_active ? 'Nonaktifkan' : 'Aktifkan' }} meja {{ $table->table_number }}?">
                        @csrf @method('patch')
                        <button class="toggle" type="submit" role="switch" aria-checked="{{ $table->is_active ? 'true' : 'false' }}" aria-label="{{ $table->is_active ? 'Nonaktifkan' : 'Aktifkan' }} meja {{ $table->table_number }}"></button>
                    </form>
                    <div class="table-actions">
                        <a class="table-action" href="{{ route('tables.edit', $table) }}" aria-label="Edit meja {{ $table->table_number }}"><x-icon name="edit" /></a>
                        <form action="{{ route('tables.destroy', $table) }}" method="post" data-confirm="Hapus meja {{ $table->table_number }}?">
                            @csrf @method('delete')
                            <button class="table-action delete" type="submit" aria-label="Hapus meja {{ $table->table_number }}"><x-icon name="trash" /></button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-state">Belum ada meja. Gunakan tombol “Tambah Meja” untuk mengisi daftar.</p>
        @endforelse
    </section>
    <x-modal id="add-table" title="Tambah Meja">
        <form class="payment-form" action="{{ route('tables.store') }}" method="post">
            @csrf
            <div class="form-group"><label class="field-label" for="table-number">Nomor meja</label><input class="input-control" id="table-number" name="table_number" value="{{ old('table_number') }}" required placeholder="Contoh: A04"></div>
            <div class="form-group"><label class="field-label" for="table-capacity">Kapasitas kursi</label><input class="input-control" id="table-capacity" name="capacity" type="number" min="1" value="{{ old('capacity') }}" placeholder="Contoh: 2" required></div>
            <div class="form-group"><label class="field-label" for="table-location">Lokasi</label><input class="input-control" id="table-location" name="location" value="{{ old('location') }}" placeholder="Area depan"></div>
            <div class="modal-actions"><button type="button" class="btn btn-outline" data-modal-close>Batal</button><button class="btn btn-primary" type="submit">Simpan meja</button></div>
        </form>
    </x-modal>
@endsection
