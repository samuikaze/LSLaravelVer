<?php

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
})->name('index');

Route::get('/about', 'Frontend\AboutController@index')->name('about');

Route::get('/contact', 'Frontend\ContactController@index')->name('contact');

Route::get('/faq', 'Frontend\FaqController@index')->name('faq');

Route::get('/product', 'Frontend\ProductController@index')->name('product');

Route::get('/goods/{page?}', 'Frontend\GoodsController@index')->name('goods');
