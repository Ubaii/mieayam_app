<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login', ['registrationAvailable' => ! User::query()->exists()]);
    }

    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        if (! $request->user()->isAdmin()) {
            $request->session()->forget('url.intended');

            return redirect()->route('cashier');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function createRegistration(): View|RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('login')
                ->with('status', 'Pendaftaran awal sudah ditutup. Silakan masuk dengan akun Anda.');
        }

        return view('auth.register');
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Pendaftaran awal hanya tersedia sekali.']);
        }

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $attributes['name'],
            'email' => Str::lower($attributes['email']),
            'password' => $attributes['password'],
            'role' => 'admin',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('status', 'Akun administrator berhasil dibuat.');
    }

    public function createForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return back()->with('status', __($status));
    }

    public function createResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function storeResetPassword(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $status = Password::reset(
            $attributes,
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])
                    ->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', __($status));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->user()->forceFill(['remember_token' => Str::random(60)])->save();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda sudah keluar. Masukkan kembali email dan password untuk masuk.');
    }
}
