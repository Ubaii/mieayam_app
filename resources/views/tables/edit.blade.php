@extends('layouts.app')

@section('title', 'Edit Meja')

@section('content')
    <div class="page-heading"><div><h1>Edit Meja</h1><p>Perbarui informasi meja {{ $table->table_number }}.</p></div><a href="{{ route('tables.index') }}" class="btn btn-outline">Kembali</a></div>
    <section class="panel form-panel">
        <form class="payment-form" action="{{ route('tables.update', $table) }}" method="post">
            @csrf @method('put')
            <div class="form-group"><label class="field-label" for="table-number">Nomor meja</label><input class="input-control" id="table-number" name="table_number" value="{{ old('table_number', $table->table_number) }}" required maxlength="30"></div>
            <div class="form-group"><label class="field-label" for="table-capacity">Kapasitas kursi</label><input class="input-control" id="table-capacity" name="capacity" type="number" min="1" max="100" value="{{ old('capacity', $table->capacity) }}" required></div>
            <div class="form-group"><label class="field-label" for="table-location">Lokasi</label><input class="input-control" id="table-location" name="location" value="{{ old('location', $table->location) }}" maxlength="120"></div>
            <div class="form-group"><label class="field-label" for="table-status">Status</label><select class="select-control" id="table-status" name="status">@foreach(['available' => 'Tersedia', 'occupied' => 'Terisi', 'reserved' => 'Reserved', 'maintenance' => 'Maintenance'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $table->status) === $value)>{{ $label }}</option>@endforeach</select></div>
            <input type="hidden" name="is_active" value="0">
            <div class="form-group"><label class="login-remember"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $table->is_active))> Meja aktif</label></div>
            <div class="modal-actions"><a href="{{ route('tables.index') }}" class="btn btn-outline">Batal</a><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
        </form>
    </section>
@endsection
