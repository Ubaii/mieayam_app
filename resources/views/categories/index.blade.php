@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="page-heading">
    <div>
        <h1>Kategori Menu</h1>
        <p>Kelompokkan menu agar lebih mudah dikelola.</p>
    </div>
    <div class="heading-actions">
        <button type="button" class="btn btn-primary" data-modal-open="add-category"><x-icon name="plus" /> Tambah</button>
    </div>
</div>
<div class="filter-bar">
    <form class="filter-field search-control" method="get" action="{{ route('categories.index') }}"><x-icon name="search" /><input class="input-control" type="search" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."></form>
</div>
<section class="management-grid">
    @forelse($categories as $category)
    <article class="management-card">
        <div class="table-management-top">
            <h3>{{ $category->name }}</h3><span class="badge {{ $category->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <p>{{ $category->description ?: 'Belum ada deskripsi.' }}</p>
        <div class="management-card-footer"><small>{{ $category->menus_count }} menu</small>
            <div class="management-card-actions">
                <a class="table-action" href="{{ route('categories.edit', $category) }}" aria-label="Edit kategori {{ $category->name }}"><x-icon name="edit" /></a>
                <form action="{{ route('categories.destroy', $category) }}" method="post" data-confirm="Hapus kategori {{ $category->name }}?">
                    @csrf @method('delete')
                    <button
                        type="button"
                        class="table-action delete"
                        aria-label="Hapus kategori {{ $category->name }}"
                        data-modal-open="delete-category-{{ $category->id }}">
                        <x-icon name="trash" />
                    </button>
                </form>
            </div>
        </div>
    </article>

    <x-modal
        id="delete-category-{{ $category->id }}"
        title="Hapus Kategori">
        <div class="delete-modal-content">

            <p>
                Hapus kategori "{{ $category->name }}"?
            </p>

            <p>
                Kategori ini akan dihapus secara permanen dan tidak dapat dikembalikan.
            </p>

            <form
                action="{{ route('categories.destroy', $category) }}"
                method="post">
                @csrf
                @method('delete')

                <div class="modal-actions">

                    <button
                        type="button"
                        class="btn btn-outline"
                        data-modal-close>
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">
                        Hapus Kategori
                    </button>

                </div>
            </form>

        </div>
    </x-modal>
    @empty
    <p class="empty-state">Belum ada kategori. Gunakan tombol “Tambah Kategori” untuk mengisi daftar.</p>
    @endforelse
</section>
<x-modal id="add-category" title="Tambah Kategori">
    <form class="payment-form" action="{{ route('categories.store') }}" method="post">
        @csrf
        <div class="form-group"><label class="field-label" for="category-name">Nama kategori</label><input class="input-control" id="category-name" name="name" value="{{ old('name') }}" required placeholder="Nama kategori"></div>
        <div class="form-group"><label class="field-label" for="category-description">Deskripsi</label><textarea class="textarea-control" id="category-description" name="description" placeholder="Deskripsi singkat kategori">{{ old('description') }}</textarea></div>
        <div class="modal-actions"><button type="button" class="btn btn-outline" data-modal-close>Batal</button><button class="btn btn-primary" type="submit">Simpan kategori</button></div>
    </form>
</x-modal>
@endsection