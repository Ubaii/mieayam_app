@extends('layouts.app')

@section('title', 'Edit Menu')

@section('content')
    <div class="page-heading"><div><h1>Edit Menu</h1><p>Perbarui informasi {{ $menu->name }}.</p></div><a href="{{ route('menus.index') }}" class="btn btn-outline">Kembali</a></div>
    <section class="panel form-panel">
        <form class="payment-form" action="{{ route('menus.update', $menu) }}" method="post">
            @csrf @method('put')
            <div class="form-group"><label class="field-label" for="menu-name">Nama menu</label><input class="input-control" id="menu-name" name="name" value="{{ old('name', $menu->name) }}" required maxlength="120"></div>
            <div class="form-group"><label class="field-label" for="menu-description">Deskripsi</label><textarea class="textarea-control" id="menu-description" name="description" maxlength="1000">{{ old('description', $menu->description) }}</textarea></div>
            <div class="form-group"><label class="field-label" for="menu-category">Kategori</label><select class="select-control" id="menu-category" name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $menu->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="form-group"><label class="field-label" for="menu-price">Harga (Rp)</label><input class="input-control" id="menu-price" name="price" type="number" min="1" value="{{ old('price', $menu->price) }}" required></div>
            <div class="form-group"><label class="field-label" for="menu-status">Ketersediaan menu</label><select class="select-control" id="menu-status" name="is_active"><option value="1" @selected((string) old('is_active', (int) $menu->is_active) === '1')>Aktif — tersedia untuk dipesan</option><option value="0" @selected((string) old('is_active', (int) $menu->is_active) === '0')>Nonaktif — kosong sementara</option></select></div>
            <div class="modal-actions"><a href="{{ route('menus.index') }}" class="btn btn-outline">Batal</a><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
        </form>
    </section>
@endsection
