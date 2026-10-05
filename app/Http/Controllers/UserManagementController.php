<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->withCount('transactions')->orderBy('name')->get(),
            'adminCount' => User::query()->where('role', 'admin')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower($request->string('email')->toString()),
        ]);

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'cashier'])],
        ]);

        User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => $attributes['role'],
        ]);

        $accountType = $attributes['role'] === 'admin' ? 'administrator' : 'pegawai kasir';

        return redirect()->route('users.index')->with('status', "Akun {$accountType} berhasil dibuat.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $error = DB::transaction(function () use ($request, $user): ?string {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if ($user->is($request->user())) {
                return 'Akun yang sedang digunakan tidak dapat dihapus.';
            }

            if ($user->isAdmin()) {
                $admins = User::query()->where('role', 'admin')->lockForUpdate()->get(['id']);

                if ($admins->count() <= 1) {
                    return 'Administrator terakhir tidak dapat dihapus. Buat administrator lain terlebih dahulu.';
                }
            }

            if ($user->transactions()->exists()) {
                return 'Akun dengan riwayat transaksi tidak dapat dihapus agar catatan transaksi tetap tersimpan.';
            }

            $user->delete();

            return null;
        });

        if ($error !== null) {
            return redirect()->route('users.index')->withErrors(['user' => $error]);
        }

        return redirect()->route('users.index')->with('status', 'Akun berhasil dihapus.');
    }
}
