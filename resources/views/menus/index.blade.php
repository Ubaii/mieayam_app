@extends('layouts.app')

@section('title', 'Manajemen Menu')

@section('content')
<div class="page-heading">
    <div>
        <h1>Manajemen Menu</h1>
        <p>Atur produk dan ketersediaan menu.</p>
    </div>
    <div class="heading-actions">
        <button type="button" class="btn btn-primary" data-modal-open="add-menu"><x-icon name="plus" /> Tambah</button>
    </div>
</div>
@if($categories->where('is_active', true)->isEmpty())
<p class="form-alert success">Buat dan aktifkan kategori terlebih dahulu sebelum menambahkan menu. <a href="{{ route('categories.index') }}" class="text-link">Kelola kategori</a></p>
@endif
<section class="panel">
    <form class="filter-bar" method="get" action="{{ route('menus.index') }}">
        <div class="filter-field search-control"><x-icon name="search" /><input class="input-control" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."></div>
        <div class="filter-field"><label class="sr-only" for="menu-category">Kategori</label><select id="menu-category" name="category_id" class="select-control">
                <option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>{{ $category->name }}</option>@endforeach
            </select></div>
        <div class="filter-field"><label class="sr-only" for="menu-status">Status</label><select id="menu-status" name="status" class="select-control">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status')==='active' )>Aktif</option>
                <option value="inactive" @selected(request('status')==='inactive' )>Nonaktif (kosong sementara)</option>
            </select></div>
        <button class="btn btn-outline" type="submit">Terapkan</button>
    </form>
    @if($menus->isEmpty())
    <div class="menu-empty-state">
        <span class="menu-empty-icon"><x-icon name="coffee" /></span>
        <h2>Belum ada menu</h2>
        <p>Gunakan tombol di bawah untuk mulai menambahkan menu MIE AYAM WENGI'57.</p>
        <button type="button" class="btn btn-primary" data-modal-open="add-menu"><x-icon name="plus" /> Tambah Menu</button>
    </div>
    @else
    <x-table>
        <thead>
            <tr>
                <th>MENU</th>
                <th>KATEGORI</th>
                <th>HARGA</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($menus as $menu)
            <tr>
                <td><span class="table-primary">{{ $menu->name }}</span><span class="table-secondary">{{ $menu->description ?: 'Belum ada deskripsi.' }}</span></td>
                <td><span class="badge badge-neutral">{{ $menu->category->name }}</span></td>
                <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                <td>
                    <div class="menu-availability">
                        <span class="badge {{ $menu->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $menu->is_active ? 'Aktif' : 'Kosong sementara' }}</span>
                        <form action="{{ route('menus.status', $menu) }}" method="post">
                            @csrf @method('patch')
                            <button class="btn btn-outline menu-status-action" type="submit">{{ $menu->is_active ? 'Tandai kosong' : 'Aktifkan kembali' }}</button>
                        </form>
                    </div>
                </td>
                <td>
                    <div class="table-actions">
                        <a class="table-action" href="{{ route('menus.edit', $menu) }}" aria-label="Edit {{ $menu->name }}"><x-icon name="edit" /></a>
                        <form action="{{ route('menus.destroy', $menu) }}" method="post" data-confirm="Hapus menu {{ $menu->name }}?">
                            @csrf @method('delete')
                            <button
                                type="button"
                                class="table-action delete"
                                aria-label="Hapus {{ $menu->name }}"
                                data-modal-open="delete-menu-{{ $menu->id }}">
                                <x-icon name="trash" />
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            <x-modal id="delete-menu-{{ $menu->id }}" title="Hapus Menu">
                <div class="delete-modal-content">

                    <p>Hapus menu "{{ $menu->name }}"?</p>

                    <p>
                        Menu ini akan dihapus secara permanen dan tidak dapat dikembalikan.
                    </p>

                    <form
                        action="{{ route('menus.destroy', $menu) }}"
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
                                Hapus Menu
                            </button>
                        </div>
                    </form>

                </div>
            </x-modal>
            @endforeach
        </tbody>
    </x-table>
    @endif
</section>
<x-modal id="add-menu" title="Tambah Menu">
    <form class="payment-form" action="{{ route('menus.store') }}" method="post">
        @csrf
        <div class="form-group"><label class="field-label" for="new-menu-name">Nama menu</label><input class="input-control" id="new-menu-name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Signature Senja"></div>
        <div class="form-group"><label class="field-label" for="new-menu-desc">Deskripsi</label><textarea class="textarea-control" id="new-menu-desc" name="description" placeholder="Ceritakan menu ini secara singkat">{{ old('description') }}</textarea></div>
        <div class="form-group"><label class="field-label" for="new-menu-category">Kategori</label><select class="select-control" id="new-menu-category" name="category_id" required>
                <option value="">Pilih kategori</option>@foreach($categories->where('is_active', true) as $category)<option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ $category->name }}</option>@endforeach
            </select></div>
        <div class="form-group"><label class="field-label" for="new-menu-price">Harga</label><input class="input-control" id="new-menu-price" name="price" type="number" min="1" value="{{ old('price') }}" placeholder="25000" required></div>
        <div class="form-group"><label class="field-label" for="new-menu-status">Ketersediaan menu</label><select class="select-control" id="new-menu-status" name="is_active">
                <option value="1" @selected(old('is_active', '1' )==='1' )>Aktif — tersedia untuk dipesan</option>
                <option value="0" @selected(old('is_active')==='0' )>Nonaktif — kosong sementara</option>
            </select></div>
        <div class="modal-actions"><button type="button" class="btn btn-outline" data-modal-close>Batal</button><button class="btn btn-primary" type="submit">Simpan menu</button></div>
    </form>
</x-modal>
@endsection