<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CafeTableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route(auth()->user()->isAdmin() ? 'dashboard' : 'cashier')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin'])->middleware('throttle:5,1')->name('login.store');

    Route::get('/register', [AuthController::class, 'createRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->middleware('throttle:5,1')->name('register.store');

    Route::get('/forgot-password', [AuthController::class, 'createForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'createResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'storeResetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/cashier', [TransactionController::class, 'cashier'])->name('cashier');
    Route::post('/cashier/checkout', [TransactionController::class, 'store'])->name('cashier.checkout');
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->middleware('throttle:5,1')->name('users.store');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::patch('/menus/{menu}/status', [MenuController::class, 'toggleStatus'])->name('menus.status');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

        Route::get('/tables', [CafeTableController::class, 'index'])->name('tables.index');
        Route::post('/tables', [CafeTableController::class, 'store'])->name('tables.store');
        Route::get('/tables/{table}/edit', [CafeTableController::class, 'edit'])->name('tables.edit');
        Route::put('/tables/{table}', [CafeTableController::class, 'update'])->name('tables.update');
        Route::patch('/tables/{table}/active', [CafeTableController::class, 'toggleActive'])->name('tables.active.toggle');
        Route::patch('/tables/{table}/status', [TransactionController::class, 'updateTableStatus'])->name('tables.status.update');
        Route::delete('/tables/{table}', [CafeTableController::class, 'destroy'])->name('tables.destroy');
        Route::get('/table-status', [CafeTableController::class, 'status'])->name('tables.status');

        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/receipt', function () {
            $transaction = Transaction::query()->latest('paid_at')->first();

            return $transaction
                ? redirect()->route('transactions.receipt', $transaction)
                : view('transactions.receipt', ['transaction' => null]);
        })->name('receipt.latest');

        Route::get('/reports', ReportController::class)->name('reports.index');
	        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });
});
