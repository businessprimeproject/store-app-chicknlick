<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::post('/register', [HomeController::class, 'storeRegistration'])->name('register.store');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::get('/orders', [HomeController::class, 'orders'])->name('orders');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::get('/tracking', [HomeController::class, 'tracking'])->name('tracking');
Route::get('/tracking/receipt', [HomeController::class, 'receipt'])->name('tracking.receipt');
Route::post('/cart/submit', [HomeController::class, 'submitOrder'])->name('cart.submit');
Route::post('/generate', [HomeController::class, 'generate'])->name('generate');
Route::get('/admin/login', [HomeController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/login', [HomeController::class, 'adminAuthenticate'])->name('admin.authenticate');
Route::get('/admin', [HomeController::class, 'admin'])->name('admin.dashboard');
Route::post('/admin/franchisees', [HomeController::class, 'adminStoreFranchisee'])->name('admin.franchisees.store');
Route::patch('/admin/franchisees/{user}/approve', [HomeController::class, 'adminApproveFranchisee'])->name('admin.franchisees.approve');
Route::delete('/admin/franchisees/{user}', [HomeController::class, 'adminDeleteFranchisee'])->name('admin.franchisees.destroy');
Route::post('/admin/orders/{orderNumber}/status', [HomeController::class, 'adminUpdateOrderStatus'])->name('admin.orders.status');
Route::get('/admin/orders/{orderNumber}/receipt', [HomeController::class, 'adminReceipt'])->name('admin.orders.receipt');
Route::post('/admin/logout', [HomeController::class, 'adminLogout'])->name('admin.logout');
