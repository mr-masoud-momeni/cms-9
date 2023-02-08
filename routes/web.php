<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', function () {
    return view('welcome');
});
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
        'middleware'=>['auth' , 'verified'],
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
    Route::resource('/product', 'productController');
    Route::get('/category/create/product' , 'CategoryController@create')->name('catProduct.create');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/upload-image', 'panelAdmin@UploadImageInText')->name('uploadImage');
    Route::get('search','HomeController@search')->name('search');
    Route::prefix('jobs')->group(function () {
        Route::queueMonitor();
    });
//    Route::get('/menu' , function (){
//       return view('Backend.menu.create');
//    });
});
require __DIR__.'/auth.php';
