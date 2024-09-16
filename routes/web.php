<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\customer\LoginController;
use App\Http\Controllers\front\BuyerController;

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
        Route::get('/blog/{article}', 'blog@show')->name('article.show');
        Route::get('/page/{page}', 'blog@show1')->name('page.show');
        Route::post('/buy' , 'BuyController@add_order')->name('buy.add');
        Route::resource('/order', 'OrderController');
});
//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/event', function () {
    event (new \App\Events\NewTrade('test'));
});

Route::get('/dashboard', function () {
    return view('Backend.layouts.Master');
})->middleware(['auth' , 'verified'])->name('dashboard');
//Route::name('admin.')->group(function () {
//    Route::resource('/Permission', 'PermissionController');
//    Route::resource('/role', 'RoleController');
//});

Route::group(
    [
        'middleware'=>['auth' , 'verified', 'role:admin'],
        'namespace'=> 'App\Http\Controllers\admin',
        'prefix' => 'admin',
    ]
    , function () {
    Route::resource('/register' , 'UserController');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/test','RoleController@create');
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
//    Route::resource('/product', 'productController');
//    Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/upload-image', 'panelAdmin@UploadImageInText')->name('uploadImage');
    Route::get('search','HomeController@search')->name('search');
});
require __DIR__.'/auth.php';

Route::get('/customer/login/{token}', [LoginController::class, 'showLoginForm'])->name('customer.login.path');
Route::post('/customer/login', [LoginController::class, 'login'])->name('customer.login');
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
    Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
    Route::post('/category/create', 'CategoryController@save')->name('catProduct.save');
    Route::patch('/category/edit', 'CategoryController@edit')->name('catProduct.edit');
    Route::delete('/category/delete', 'CategoryController@delete')->name('catProduct.delete');
});

Route::get('/buyerregister', [BuyerController::class, 'index'])->name('buyer.show.register');
Route::post('/buyerregister', [BuyerController::class, 'register'])->name('buyer.register');
Route::get('/verify-email-user/{uuid}/{token}', [BuyerController::class, 'verifyEmail'])->name('buyer.verify.email');
