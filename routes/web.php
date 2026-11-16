<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/meja/{table}', [StorefrontController::class, 'table'])->whereNumber('table')->name('table.menu');
Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('authenticate');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/pesanan/riwayat', [AdminController::class, 'orderHistory'])->name('orders.history');
        Route::get('/pesanan', [AdminController::class, 'orders'])->name('orders');
        Route::patch('/pesanan/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
        Route::get('/menu', [AdminController::class, 'menu'])->name('menu');
        Route::get('/menu/{product}/edit', [AdminController::class, 'editMenu'])->name('menu.edit');
        Route::patch('/menu/{product}', [AdminController::class, 'updateMenu'])->name('menu.update');
        Route::delete('/menu/{product}', [AdminController::class, 'deleteMenu'])->name('menu.delete');
        Route::get('/meja', [AdminController::class, 'tables'])->name('tables');
        Route::post('/menu', [AdminController::class, 'saveMenu'])->name('menu.save');
        Route::patch('/menu/{product}/stok', [AdminController::class, 'toggleStock'])->name('menu.stock');
        Route::get('/kanal', [AdminController::class, 'channels'])->name('channels');
        Route::post('/kanal/pesanan', [AdminController::class, 'createExternalOrder'])->name('channels.order');
    });
});
