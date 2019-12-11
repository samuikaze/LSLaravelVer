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

// sessions 的 Middleware Group 可以到 app\Http\Kernel.php 查閱
Route::group(['middleware' => ['sessions']], function () {
    // 首頁
    Route::get('/', 'Frontend\IndexController@index')->name('index');

    // 最新消息
    Route::get('/news/{page?}', 'Frontend\NewsController@index')->name('news');

    // 消息詳細
    Route::get('/news/detail/{id}', 'Frontend\NewsController@show')->name('news.detail');

    // 作品一覽
    Route::get('/product', 'Frontend\ProductController@index')->name('product');

    // 商品一覽
    Route::get('/goods/{page?}', 'Frontend\GoodsController@index')->name('goods');

    // 商品詳細
    Route::get('/goods/goodDetail/{goodId}', 'Frontend\GoodsController@show')->name('gooddetail');

    // 討論區（選擇討論板）
    Route::get('/bbs', 'Frontend\BBSController@index')->name('boardselect');

    // 討論區（討論板/討論串）
    Route::get('/bbs/{bid}', 'Frontend\BBSController@show')->name('showboard');

    // 檢視討論串
    Route::get('bbs/{bid}/post/{postid}', 'Frontend\BBSController@view')->name('viewdiscussion');

    // 關於團隊
    Route::get('/about', 'Frontend\AboutController@index')->name('about');

    // 招募新血
    Route::get('/recruit', 'Frontend\RecruitController@index')->name('recruit');

    // 常見問題
    Route::get('/faq', 'Frontend\FaqController@index')->name('faq');

    // 連絡我們
    Route::get('/contact', 'Frontend\ContactController@index')->name('contact');
});

// 登入/註冊頁面
Route::get('/useraction', 'Auth\LoginController@showForm')->name('useraction');

// 執行登入
Route::post('/login', 'Auth\LoginController@login')->name('login');

// 執行登出
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// 執行註冊
Route::post('/register', 'Auth\RegisterController@register')->name('register');