<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\customer\LoginController;
use App\Http\Controllers\front\BuyerController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\customer\GatewayController;
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
        Route::get('/product/{product}', 'ProductController@show')->name('front.product.show');
        Route::get('/blog/{article}', 'blog@show')->name('article.show');
        Route::get('/page/{page}', 'blog@show1')->name('page.showw');
        Route::post('/buy' , 'BuyController@add_order')->name('buy.add');
        Route::resource('/order', 'OrderController');

});

Route::get('/event', function () {
    event (new \App\Events\NewTrade('test'));
});

Route::get('/dashboard', function () {
    return view('Backend.layouts.Master');
})->middleware(['auth' , 'verified'])->name('dashboard');

Route::group(
    [
        'middleware'=>['auth' , 'verified', 'role:admin'],
        'namespace'=> 'App\Http\Controllers\admin',
        'prefix' => 'admin',
    ]
    , function () {
    Route::resource('/register' , 'UserController');
    Route::get('/home', 'HomeController@index')->name('home');
//    Route::get('/test','RoleController@create');
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
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/upload-image', 'panelAdmin@UploadImageInText')->name('uploadImage');
    Route::get('search','HomeController@search')->name('search');
});
require __DIR__.'/auth.php';

Route::get('/customer/login/{path}', [LoginController::class, 'showLoginForm'])->name('customer.login.path');
Route::post('/customer/login/{path}', [LoginController::class, 'login'])->name('customer.login');
Route::post('/customer/logout', [LoginController::class, 'logout'])->middleware(['auth' , 'verified'])->name('customer.logout');
Route::get('/customer/dashboard', [LoginController::class, 'dashboard'])->middleware(['auth' , 'verified'])->name('customer.dashboard');

Route::group(
    [
        'middleware'=>['auth' , 'verified', 'role:shop_owner'],
        'namespace'=> 'App\Http\Controllers\customer',
        'prefix' => 'customer',
    ]
    , function () {
    Route::resource('/product', 'ProductController');
    Route::get('/gateways', [GatewayController::class, 'edit'])->name('gateways.edit');
    Route::post('/gateways', [GatewayController::class, 'update'])->name('gateways.update');
    Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
    Route::post('/category/create', 'CategoryController@save')->name('catProduct.save');
    Route::patch('/category/edit', 'CategoryController@edit')->name('catProduct.edit');
    Route::delete('/category/delete', 'CategoryController@delete')->name('catProduct.delete');
});

//buyer routes
Route::get('/buyer/register', [BuyerController::class, 'index'])->name('buyer.show.register');
Route::post('/buyer/register', [BuyerController::class, 'register'])->name('buyer.register');
Route::get('/verify-email-user/{uuid}/{token}', [BuyerController::class, 'verifyEmail'])->name('buyer.verify.email');
// نمایش فرم لاگین و مدیریت لاگین خریدار
Route::get('/buyer/login', [BuyerController::class, 'showLoginForm'])->name('buyer.login.path');
Route::post('/buyer/login', [BuyerController::class, 'login'])->name('buyer.login');

// خروج (logout) خریدار
Route::get('/buyer/logout', [BuyerController::class, 'logout'])->name('buyer.logout');
Route::get('/buyer/dashboard', [BuyerController::class, 'dashboard'])->middleware(['auth.buyer','buyer.verified','checkRole:buyer,buyer'])->name('buyer.dashboard');




