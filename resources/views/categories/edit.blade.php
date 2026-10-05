@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="page-heading"><div><h1>Edit Kategori</h1><p>Perbarui informasi kategori {{ $category->name }}.</p></div><a href="{{ route('categories.index') }}" class="btn btn-outline">Kembali</a></div>
    <section class="panel form-panel">
        <form class="payment-form" action="{{ route('categories.update', $category) }}" method="post">
            @csrf @method('put')
            <div class="form-group"><label class="field-label" for="category-name">Nama kategori</label><input class="input-control" id="category-name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100"></div>
            <div class="form-group"><label class="field-label" for="category-description">Deskripsi</label><textarea class="textarea-control" id="category-description" name="description" maxlength="1000">{{ old('description', $category->description) }}</textarea></div>
            <input type="hidden" name="is_active" value="0">
            <div class="form-group"><label class="login-remember"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $category->is_active))> Kategori aktif</label></div>
            <div class="modal-actions"><a href="{{ route('categories.index') }}" class="btn btn-outline">Batal</a><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
        </form>
    </section>
@endsection
