<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\front\BuyerController;
use App\Http\Controllers\front\OrderController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\customer\GatewayController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ShopAdminLoginController;
use App\Http\Controllers\Auth\BuyerLoginController;
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
        'namespace'=> 'App\Http\Controllers\front',
    ]
    , function () {
        Route::get('/','IndexController@index')->name('index.show');
        Route::get('/shop','IndexController@shop')->name('index.shop');
        Route::get('/product/{product}', 'IndexController@product')->name('front.product.show');
        Route::post('/pay','PaymentController@init')->name('payment');
        Route::post('/pay/callback','PaymentController@callback')->name('payments.callback');
        Route::get('/pay/success/{payment}','PaymentController@success')->name('payments.success');
        Route::get('/pay/failed/{payment}','PaymentController@failed')->name('payments.failed');
        Route::get('/blog/{article}', 'blog@show')->name('article.show');
        Route::get('/page/{page}', 'blog@show1')->name('page.showw');
        Route::post('/buy' , 'BuyController@add_order')->name('buy.add');
        Route::resource('/order', 'OrderController');

});

// ادمین اصلی
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});
Route::group(
    [
        'middleware'=>['auth' , 'verified', 'role:admin'],
        'namespace'=> 'App\Http\Controllers\admin',
        'prefix' => 'admin',
    ]
    , function () {

    Route::get('/dashboard', function () {return view('Backend.layouts.Master');})->name('admin.dashboard');
    Route::resource('/register' , 'UserController');
    Route::get('/article', 'ArticleController@index')->name('article.index');
    Route::get('/article/edit/{article}', 'ArticleController@edit')->name('article.edit');
    Route::get('/article/create', 'ArticleController@create')->name('article.create');
    Route::patch('/article/{article}', 'ArticleController@update')->name('article.update');
    Route::delete('/article/delete', 'ArticleController@delete')->name('article.delete');
    Route::post('/article', 'ArticleController@save')->name('article.save');
    Route::get('/category/create/article', 'CategoryController@create')->name('catArticle.create');
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
    Route::get('search','HomeController@search')->name('search');
});
// ادمین فروشگاه
Route::prefix('shop/{path}')->group(function () {
    Route::get('/login', [ShopAdminLoginController::class, 'showLoginForm'])->name('shop.login');
    Route::post('/login', [ShopAdminLoginController::class, 'login']);
    Route::post('/logout', [ShopAdminLoginController::class, 'logout'])->name('shop.logout');
});
Route::group(
    [
        'middleware'=>['auth:shop_admin' , 'verified', 'role:shop_owner' , 'check.shop'],
        'namespace'=> 'App\Http\Controllers\customer',
        'prefix' => 'shop',
        'as' => 'shop.',
    ]
    , function () {
    Route::get('/dashboard', function () {return view('Customer.layouts.Master');})->name('dashboard');
    Route::resource('/product', 'ProductController');
    Route::resource('/Orders', 'OrderController');
    Route::get('/gateways', [GatewayController::class, 'edit'])->name('gateways.edit');
    Route::post('/gateways', [GatewayController::class, 'store'])->name('gateways.store');
    Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
    Route::post('/category/create', 'CategoryController@save')->name('catProduct.save');
    Route::patch('/category/edit', 'CategoryController@edit')->name('catProduct.edit');
    Route::delete('/category/delete', 'CategoryController@delete')->name('catProduct.delete');
});
// خریدار
Route::prefix('buyer')->group(function () {
    Route::get('/register', [BuyerController::class, 'index'])->name('buyer.show.register');
    Route::post('/register', [BuyerController::class, 'register'])->name('buyer.register');
    Route::get('/login', [BuyerController::class, 'showLoginForm'])->name('buyer.login.path');
    Route::post('/login', [BuyerController::class, 'login'])->name('buyer.login');
    Route::get('/logout', [BuyerController::class, 'logout'])->name('buyer.logout');
});
Route::get('/verify-email-user/{uuid}/{token}', [BuyerController::class, 'verifyEmail'])->name('buyer.verify.email');
Route::group(
    [
        'middleware'=>['auth.buyer','buyer.verified','role.buyer:buyer'],
        'prefix' => 'buyer',
        'as' => 'buyer.',
    ]
    , function () {
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/order/completed', [OrderController::class, 'completedOrders'])->name('orders.completed');

});



