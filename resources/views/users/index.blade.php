@extends('layouts.app')

@section('title', 'Akun Pegawai')

@section('content')
    <div class="page-heading">
        <div><h1>Akun Pegawai</h1><p>Buat akun administrator atau kasir untuk MIE AYAM WENGI'57.</p></div>
    </div>

    <div class="user-management-grid">
        <section class="panel">
            <div class="panel-heading"><div><h2>Buat akun baru</h2><p>Administrator memiliki akses penuh; kasir hanya dapat menggunakan halaman kasir.</p></div></div>
            <form class="payment-form" action="{{ route('users.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label class="field-label" for="staff-name">Nama pegawai</label>
                    <input class="input-control" id="staff-name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div class="form-group">
                    <label class="field-label" for="staff-email">Email untuk login</label>
                    <input class="input-control" id="staff-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label class="field-label" for="account-role">Peran akun</label>
                    <select class="input-control" id="account-role" name="role" required>
                        <option value="cashier" @selected(old('role', 'cashier') === 'cashier')>Kasir</option>
                        <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="field-label" for="staff-password">Password awal</label>
                    <input class="input-control" id="staff-password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                </div>
                <div class="form-group">
                    <label class="field-label" for="staff-password-confirmation">Ulangi password</label>
                    <input class="input-control" id="staff-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                </div>
                <button class="btn btn-primary" type="submit">Buat akun</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-heading"><div><h2>Daftar akun</h2><p>Akun administrator dan pegawai yang bisa masuk.</p></div></div>
            @if($users->isEmpty())
                <div class="empty-state">Belum ada akun pegawai.</div>
            @else
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>NAMA</th><th>EMAIL</th><th>PERAN</th><th>AKSI</th></tr></thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td><span class="table-primary">{{ $user->name }}</span></td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge {{ $user->isAdmin() ? 'badge-info' : 'badge-success' }}">{{ $user->isAdmin() ? 'Administrator' : 'Kasir' }}</span></td>
                                    <td>
                                        @if($user->is(auth()->user()) && $user->isAdmin() && $adminCount <= 1)
                                            <span class="table-secondary">Akun aktif & administrator terakhir</span>
                                        @elseif($user->is(auth()->user()))
                                            <span class="table-secondary">Akun yang sedang digunakan</span>
                                        @elseif($user->isAdmin() && $adminCount <= 1)
                                            <span class="table-secondary">Administrator terakhir</span>
                                        @elseif($user->transactions_count > 0)
                                            <span class="table-secondary">Ada riwayat transaksi</span>
                                        @else
                                            <form action="{{ route('users.destroy', $user) }}" method="post" data-confirm="Hapus akun {{ $user->name }} ({{ $user->isAdmin() ? 'Administrator' : 'Kasir' }})? Akun ini tidak dapat digunakan lagi untuk masuk.">
                                                @csrf @method('delete')
                                                <button class="table-action delete" type="submit" aria-label="Hapus akun {{ $user->name }}"><x-icon name="trash" /></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
