@extends('layouts.app')

@section('title', 'Status Meja')

@section('content')
    <div class="page-heading"><div><h1>Status Meja</h1><p>Pantau ketersediaan meja secara langsung.</p></div><a class="btn btn-outline" href="{{ route('tables.index') }}"><x-icon name="table" /> Kelola meja</a></div>
    @php($statusLabels = ['available' => 'Tersedia', 'occupied' => 'Terisi', 'reserved' => 'Reserved', 'maintenance' => 'Maintenance'])
    <section class="status-summary">
        <x-stat-card label="Total Meja" :value="$tables->count()" icon="table" tone="blue" />
        <x-stat-card label="Tersedia" :value="$tables->where('status', 'available')->count()" icon="eye" tone="green" />
        <x-stat-card label="Terisi" :value="$tables->where('status', 'occupied')->count()" icon="cashier" tone="amber" />
        <x-stat-card label="Reserved" :value="$tables->where('status', 'reserved')->count()" icon="calendar" tone="violet" />
        <x-stat-card label="Maintenance" :value="$tables->where('status', 'maintenance')->count()" icon="clock" tone="teal" />
    </section>
    <section class="panel">
        <div class="panel-header"><div><h2>Denah meja</h2><p>Pilih status untuk melihat area yang dibutuhkan.</p></div><select class="select-control" style="max-width:190px" data-room-filter aria-label="Filter status meja"><option value="">Semua status</option><option>Tersedia</option><option>Terisi</option><option>Reserved</option><option>Maintenance</option></select></div>
        <div class="room-legend"><span><i class="legend-dot legend-available"></i>Tersedia</span><span><i class="legend-dot legend-occupied"></i>Terisi</span><span><i class="legend-dot legend-reserved"></i>Reserved</span><span><i class="legend-dot legend-maintenance"></i>Maintenance</span></div>
        <div class="room-grid">
            @forelse($tables as $table)
                @php($roomStyle = ['available' => 'room-available', 'occupied' => 'room-occupied', 'reserved' => 'room-reserved', 'maintenance' => 'room-maintenance'][$table->status])
                <article class="room-card {{ $roomStyle }}" data-room-status="{{ $table->status }}">
                    <span class="room-icon"><x-icon name="table" /></span>
                    <h3>{{ $table->table_number }}</h3><span class="badge badge-neutral">{{ $statusLabels[$table->status] }}</span>
                    <p>{{ $table->capacity }} kursi</p><p>{{ $table->location ?: 'Lokasi belum diatur' }}</p>
                    <form action="{{ route('tables.status.update', $table) }}" method="post" class="room-status-form">
                        @csrf @method('patch')
                        <label class="sr-only" for="room-status-{{ $table->id }}">Ubah status {{ $table->table_number }}</label>
                        <select class="select-control" id="room-status-{{ $table->id }}" name="status">
                            @foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected($table->status === $value)>{{ $label }}</option>@endforeach
                        </select>
                        <button class="btn btn-outline" type="submit">Simpan</button>
                    </form>
                </article>
            @empty
                <p class="empty-state">Belum ada meja untuk ditampilkan. Tambahkan meja dari halaman manajemen meja.</p>
            @endforelse
        </div>
    </section>
@endsection
