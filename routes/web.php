<?php

use App\Exports\OrdersExport;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductComboController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ZaloController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/home', [ProductController::class, 'home'])->name('home');
// Route::get('/', [ProductController::class, 'getProduct'])->name('getProduct');
Route::get('/product', [ProductController::class, 'getAllProduct'])->name('getAllProduct');
Route::get('/add-to-cart/{productId}', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::get('/remove-from-cart/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/increase-quantity/{productId}', [CartController::class, 'increaseQuantity'])->name('cart.increase');
Route::get('/decrease-quantity/{productId}', [CartController::class, 'decreaseQuantity'])->name('cart.decrease');
Route::get('/client.products.index', [CartController::class, 'clientProduct'])->name('client.products');
Route::post('/order/place', [CartController::class, 'place'])->name('order.place');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/order-history', [OrderController::class, 'history'])->name('orders.history')->middleware('auth');
Route::get('/order-history-affilate', [OrderController::class, 'historyAffilate'])->name('orders.history.affilate')->middleware('auth');
Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/account', [AccountController::class, 'index'])->name('account.index')->middleware('auth');
Route::get('/agency', [AccountController::class, 'agency'])->name('account.agency')->middleware('auth');
Route::get('/accoutPayment', [AccountController::class, 'accoutPayment'])->name('account.accoutPayment');
Route::get('/article/{slug}', [ArticleController::class, 'show'])->name('article_detail');
Route::get('/affilate', [ArticleController::class, 'articleAffilate'])->name('articleAffilate');
Route::get('/change_info', [UserController::class, 'editUser'])->name('editUser')->middleware('auth');
Route::put('/users/{user}', [UserController::class, 'updateUser'])->name('users.update');
Route::get('/member', [UserController::class, 'member'])->name('users.member')->middleware('auth');
Route::get('/ambassador', [UserController::class, 'ambassador'])->name('users.ambassador');
Route::get('/referrer', [UserController::class, 'referrer'])->name('users.referrer');
// Route::post('/vnpay/checkout', [CartController::class, 'checkout'])->name('vnpay.checkout');
Route::post('/order/checkout/{orderId}', [CartController::class, 'checkout'])->name('vnpay.checkout');
Route::get('/vnpay/return', [CartController::class, 'callback'])->name('vnpay.return');


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AppSettingController::class, 'openApp'])->name('admin');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('product-categories', ProductCategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('attributes', AttributeController::class);
        Route::resource('users', UserController::class);
        Route::resource('banners', BannerController::class);
        Route::patch('/banners/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
        // Route::resource('app-setting', AppSettingController::class);
        Route::get('app-setting', [AppSettingController::class, 'index'])->name('app-setting.index');
        Route::put('admin/app-setting', [AppSettingController::class, 'update'])->name('app-setting.store');
        Route::resource('categories', CategoryController::class);
        Route::resource('articles', ArticleController::class);
        Route::resource('notifications', NotificationController::class);
        Route::put('inventory/{product}/update-stock', [ProductController::class, 'updateStock'])->name('inventory.updateStock');
        Route::get('inventorys', [ProductController::class, 'indexStock'])->name('inventory.indexStock');
        Route::resource('commissions', CommissionController::class);
        Route::resource('bank-accounts', BankAccountController::class);
        Route::resource('vouchers', VoucherController::class);
        Route::resource('product-combos', ProductComboController::class);
        Route::get('/export/orders', function (Request $request) {
            return Excel::download(new OrdersExport($request), 'danh-sach-don-hang.xlsx');
        })->name('orders.export');
        Route::resource('rewards', RewardController::class);
        // routes/web.php
        Route::get('/users/{id}/detail', [UserController::class, 'detail'])->name('admin.users.detail');
        Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
        Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::post('conversations/{id}/send', [ConversationController::class, 'sendMessage'])->name('conversations.send');
        Route::get('syncRecentChats', [ConversationController::class, 'syncRecentChats'])->name('conversations.syncRecentChats');
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage']);
        Route::get('/chatZalo', [ZaloController::class, 'chatZalo']);
        Route::get('/zalo/login', [ZaloController::class, 'redirectToZalo'])->name('zalo.login');
        Route::get('/zalo/callback', [ZaloController::class, 'handleCallback'])->name('zalo.callback');
    });
    Route::post('/products/upload', [ProductController::class, 'upload'])->name('upload');
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::put('/admin/orders/{order}/updateStatusPayment', [OrderController::class, 'updateStatusPayment'])->name('admin.orders.updateStatusPayment');
    Route::get('/admin/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('admin.orders.invoice');
});

Route::get('/login', function () {
    return view('login');
})->name('login.index');
Route::get('/signup', [LoginController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [LoginController::class, 'signup'])->name('signup');

Route::get('/testnoti', function () {
    return view('testnoti');
});
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/layout2', function () {
    return view('layout2');
});

Route::get('/test', function () {
    return dd("ok");
});

// Route::get('/referrer', function () {
//     return view('referrer');
// });
