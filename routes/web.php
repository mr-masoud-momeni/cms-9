<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\front\BuyerController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\PaymentController;
use App\Http\Controllers\front\OrderTrackingController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\customer\GatewayController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ShopAdminLoginController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\customer\CardToCardController;
use App\Http\Controllers\customer\BaleConnectionController;
use App\Http\Controllers\customer\ShopSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/user/active/email/{token}','UserController@activation')->name('activation.account');
Route::group(
    [
        'middleware'=>['shop.context'],
        'namespace'=> 'App\\Http\\Controllers\\front',
    ]
    , function () {
        Route::get('/','IndexController@shop')->name('index.show');
        Route::get('/product/{product}', 'IndexController@product')->name('front.product.show');

        Route::post('/pay/callback','PaymentController@callback')->name('payments.callback');
        Route::get('/pay/success/{payment}','PaymentController@success')->name('payments.success');
        Route::get('/pay/failed/{payment}','PaymentController@failed')->name('payments.failed');
        Route::get('/blog/{article}', 'BlogController@show')->name('article.show');
        Route::post('/buy' , 'BuyController@add_order')->name('buy.add');
    }
);

// لینک عمومی و امن مشاهده سفارش مشتری
Route::get('/order/{order}/track/{expires}/{token}', [OrderTrackingController::class, 'show'])
    ->middleware('shop.context')
    ->name('customer.order.track');

// ادمین اصلی
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

Route::group(
    [
        'middleware'=>['auth' , 'verified', 'role:admin'],
        'namespace'=> 'App\\Http\\Controllers\\admin',
        'prefix' => 'admin',
    ]
    , function () {
        Route::get('/dashboard', function () {return view('Backend.layouts.Master');})->name('admin.dashboard');
        Route::resource('/register' , 'UserController');
   Route::post('/register/{uuid}/regenerate-password', 'UserController@regeneratePassword')->name('register.password.regenerate');
        Route::post('/category/create', 'CategoryController@save')->name('category.save');
        Route::patch('/category/edit', 'CategoryController@edit')->name('category.edit');
        Route::delete('/category/delete', 'CategoryController@delete')->name('category.delete');
        Route::resource('/Permission', 'PermissionController');
        Route::resource('/notification', 'NotificationController');
        Route::resource('/role', 'RoleController');
        Route::resource('/email', 'SendEmail');
        Route::resource('/email-group', 'EmailGroupController');
        Route::resource('/page', 'PageController');
        Route::resource('/menu', 'MenuController');
        Route::post('/upload-image', 'panelAdmin@UploadImageInText')->name('uploadImage');
    }
);

// ادمین فروشگاه
Route::prefix('shop/{path}')->group(function () {
    Route::get('/login', [ShopAdminLoginController::class, 'showLoginForm'])->name('shop.login');
    Route::post('/login', [ShopAdminLoginController::class, 'login']);
    Route::post('/logout', [ShopAdminLoginController::class, 'logout'])->name('shop.logout');
});

Route::group(
    [
        'middleware'=>['auth:shop_admin', 'role:shop_owner', 'check.shop'],
        'namespace'=> 'App\\Http\\Controllers\\customer',
        'prefix' => 'shop',
        'as' => 'shop.',
    ]
    , function () {
        Route::get('/dashboard', function () { return view('Customer.dashboard'); })->name('dashboard');
        Route::get('/settings', [ShopSettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [ShopSettingsController::class, 'update'])->name('settings.update');
        Route::resource('/product', 'ProductController');
        Route::post('/orders/{order}/payment/approve', 'OrderController@approvePayment')->name('orders.payment.approve');
        Route::post('/orders/{order}/payment/reject', 'OrderController@rejectPayment')->name('orders.payment.reject');
        Route::post('/orders/{order}/tracking', 'OrderController@updateTrackingCode')->name('orders.tracking.update');
        Route::resource('/orders', 'OrderController');
        Route::resource('/article', 'ArticleController');
        Route::post('/article/upload-image', 'ArticleController@uploadImageInText')->name('article.upload-image');
        Route::get('/category/create/article', 'CategoryController@create')->name('catArticle.create');
        Route::post('/category/create/article', 'CategoryController@save')->name('catArticle.save');
        Route::patch('/category/edit/article', 'CategoryController@edit')->name('catArticle.edit');
        Route::delete('/category/delete/article', 'CategoryController@delete')->name('catArticle.delete');
        Route::get('/gateways', [GatewayController::class, 'edit'])->name('gateways.edit');
        Route::post('/gateways', [GatewayController::class, 'store'])->name('gateways.store');
        Route::post('/card-to-card', [CardToCardController::class, 'update'])->name('card-to-card.store');
        Route::post('/bale/connect', [BaleConnectionController::class, 'connect'])->name('bale.connect');
        Route::post('/bale/disconnect', [BaleConnectionController::class, 'disconnect'])->name('bale.disconnect');
        Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
        Route::post('/category/create', 'CategoryController@save')->name('catProduct.save');
        Route::patch('/category/edit', 'CategoryController@edit')->name('category.edit');
        Route::delete('/category/delete', 'CategoryController@delete')->name('catProduct.delete');
    }
);

// خریدار
Route::prefix('buyer')->middleware('shop.context')->group(function () {

    // ---------- Login / Phone ----------
    Route::get('/auth/phone', [BuyerAuthController::class, 'showPhone'])
        ->name('buyer.login');

    Route::post('/auth/phone', [BuyerAuthController::class, 'submitPhone'])
        ->name('buyer.submit.phone');

    Route::get('/auth/password', [BuyerAuthController::class, 'showPassword'])
        ->name('buyer.password.form');

    Route::post('/auth/password', [BuyerAuthController::class, 'login'])
        ->name('buyer.login.submit');

    Route::post('/auth/logout', [BuyerAuthController::class, 'logout'])
        ->name('buyer.logout');

    // ---------- OTP ----------
    Route::get('/auth/otp', [BuyerAuthController::class, 'showOtpForm'])
        ->name('buyer.otp.form');

    Route::post('/auth/otp', [BuyerAuthController::class, 'verifyOtp'])
        ->name('buyer.otp.verify');

    // ---------- Register ----------
    Route::get('/auth/register', [BuyerAuthController::class, 'showRegisterForm'])
        ->name('buyer.register.form');

    Route::post('/auth/register', [BuyerAuthController::class, 'register'])
        ->name('buyer.register.submit');

    // ---------- Forgot / Reset ----------
    Route::get('/auth/forgot', [BuyerAuthController::class, 'showForgotForm'])
        ->name('buyer.forgot.form');

    Route::post('/auth/forgot', [BuyerAuthController::class, 'forgotPassword'])->name('buyer.forgot.submit');

    Route::get('/auth/reset-password', [BuyerAuthController::class, 'showResetForm'])->name('buyer.reset.form');

    Route::post('/auth/reset-password', [BuyerAuthController::class, 'resetPassword'])->name('buyer.reset.submit');
});

Route::get('/verify-email-user/{uuid}/{token}', [BuyerController::class, 'verifyEmail'])
    ->middleware('shop.context')
    ->name('buyer.verify.email');
Route::resource('buyer/order', OrderController::class)
    ->middleware('shop.context')
    ->names('buyer.order');

Route::group(
    [
        'middleware'=>['auth:buyer','buyer.verified','role:buyer','check.shop.buyer'],
        'namespace'=> 'App\\Http\\Controllers\\front',
        'prefix' => 'buyer',
        'as' => 'buyer.',
    ]
    , function () {
        Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');
        Route::get('/order/completed', [OrderController::class, 'completedOrders'])->name('orders.completed');
    }
);

// Checkout / Payment
Route::post('/checkout', [PaymentController::class, 'checkout'])->middleware('shop.context')->name('checkout');
Route::get('/payment', [PaymentController::class, 'index'])->middleware('shop.context')->name('payment.index');
Route::post('/payment/online', [PaymentController::class, 'init'])->middleware('shop.context')->name('payment.online');
Route::get('/payment/card-to-card', [PaymentController::class, 'cardToCardForm'])->middleware('shop.context')->name('payment.card_to_card');
Route::post('/payment/card-to-card', [PaymentController::class, 'cardToCard'])->middleware('shop.context')->name('payment.card_to_card.submit');
