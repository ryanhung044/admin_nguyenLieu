<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\VoucherApiController;
use App\Http\Controllers\CartController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/index', [ClientController::class, 'index']);
Route::get('/getInfoApp', [ClientController::class, 'getInfoApp']);
Route::get('/checkUser', [ClientController::class, 'checkUser']);
Route::get('/order-history-affilate', [ClientController::class, 'historyAffilate']);
Route::get('/order-history', [ClientController::class, 'historyOrder']);
Route::middleware('auth:sanctum')->get('/agency', [ClientController::class, 'agencyApi']);
Route::get('/product/{slug}', [ClientController::class, 'showProduct'])->name('product.show');
Route::get('/article/{slug}', [ClientController::class, 'showArticle']);
// Route::post('/cart/add/{productId}', [ClientController::class, 'addToCart']);
// Route::get('/cart', [ClientController::class, 'viewCart']);
Route::get('/check-stock/{productId}', [ClientController::class, 'checkStock']);
Route::get('/check-product/{id}', [ClientController::class, 'showProductId']);
Route::post('/order/place', [ClientController::class, 'place']);
Route::get('/vnpay/return', [ClientController::class, 'callback'])->name('api.vnpay.return');
Route::get('/products', [ClientController::class, 'getAllProduct']);
Route::get('/categories/{id}', [ClientController::class, 'showCategoryById']);
Route::get('/categories', [ClientController::class, 'getAllCategory']);
Route::post('/login', [ClientController::class, 'Login']);
Route::get('/members', [ClientController::class, 'getMembers']);
Route::post('/order/checkout/{orderId}', [ClientController::class, 'checkout'])->name('vnpay.checkout');

Route::patch('/orders/{order}/cancel', [ClientController::class, 'cancel']);
Route::put('/users/{user}', [ClientController::class, 'updateUser'])->name('users.update');
Route::post('/zalo/signup', [ClientController::class, 'zaloSignup']);
Route::get('/bank-info', [ClientController::class, 'BankAccount']);
Route::post('/bank-account', [ClientController::class, 'createOrUpdate']);
Route::get('/withdraw-requests', [ClientController::class, 'indexWithdrawRequest']);
Route::post('/withdraw-requests', [ClientController::class, 'storeWithdrawRequest']);
Route::post('/payment/bank-transfer/notify', [ClientController::class, 'handleZaloCallback']);
Route::get('/api/vnpay/ipn', [ClientController::class, 'handleIPN']);
Route::get('/orders', [ClientController::class, 'indexOrder']); // danh sách
Route::get('/orders/{id}', [ClientController::class, 'showOrder']); // chi tiết
Route::get('/recommended-products', [ClientController::class, 'recommended']);
Route::get('/vouchers', [VoucherApiController::class, 'index']);
Route::get('/vouchers/{code}', [VoucherApiController::class, 'show']);

// routes/web.php
Route::get('/image-proxy', function (Request $request) {
    $url = $request->query('url');
    $image = file_get_contents($url);

    return response($image)
        ->header('Content-Type', 'image/jpeg') // hoặc png, ...
        ->header('Access-Control-Allow-Origin', '*');
});
Route::post('/zalo/user-info', [ClientController::class, 'getZaloUserInfo']);
Route::get('/config/withdraw', function () {
    return response()->json([
        'withdraw_enabled' => (bool) Setting::getValue('withdraw_enabled', false)
    ]);
});
Route::post('/cart/refresh', [ClientController::class, 'refreshCart'])->name('refreshCart');
Route::post('/order/update-status-payment', [ClientController::class, 'updateStatusPayment'])->name('updateStatusPayment');
