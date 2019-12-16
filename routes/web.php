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

// 檢查 sessions 的 Middleware 可以到 app\Http\Kernel.php 查閱
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
Route::get('/bbs/{bid}/post/{postid}', 'Frontend\BBSController@view')->name('viewdiscussion');

// 關於團隊
Route::get('/about', 'Frontend\AboutController@index')->name('about');

// 招募新血
Route::get('/recruit', 'Frontend\RecruitController@index')->name('recruit');

// 常見問題
Route::get('/faq', 'Frontend\FaqController@index')->name('faq');

// 連絡我們
Route::get('/contact', 'Frontend\ContactController@index')->name('contact');

// 顯示建立討論串
Route::get('/bbs/{bid}/createpost', 'Frontend\BBSController@showCreatePostForm')
        ->name('bbs.showcreatepostform')
        ->middleware('auth');

// 顯示回覆討論串
Route::get('/bbs/{bid}/post/{postid}/replypost', 'Frontend\BBSController@showReplyPostForm')
        ->name('bbs.showreplypostform')
        ->middleware('auth');

// 顯示編輯討論串
Route::get('/bbs/{bid}/post/{postid}/edit/{type?}/{targetpost?}', 'Frontend\BBSController@editPost')
        ->name('bbs.showeditpostform')
        ->middleware('auth');

// 顯示刪除確認表單
Route::get('/bbs/{bid}/post/{postid}/delete/{type?}/{targetpost?}', 'Frontend\BBSController@showDeleteConfirmForm')
       ->name('bbs.showdelconfirm')
       ->middleware('auth');

// 執行建立討論串
Route::post('/bbs/{bid}/doCreatePost', 'Frontend\BBSController@createPost')
        ->name('bbs.createpost')
        ->middleware('auth');

// 執行建立回文
Route::post('/bbs/{bid}/post/{postid}/doReplyPost', 'Frontend\BBSController@replyPost')
        ->name('bbs.replypost')
        ->middleware('auth');

// 執行編輯文章
Route::post('/bbs/{bid}/post/{postid}/doEdit/{type?}/{targetpost?}', 'Frontend\BBSController@doEditPost')
        ->name('bbs.editpost')
        ->middleware('auth');

// 執行編輯文章
Route::post('/bbs/{bid}/post/{postid}/doDelete/{type?}/{targetpost?}', 'Frontend\BBSController@deletePost')
        ->name('bbs.delpost')
        ->middleware('auth');

// 顯示會員資料修改表單
Route::get('/dashboard', 'Frontend\DashboardController@showData')
       ->name('dashboard.form')
       ->middleware('auth');

// 執行更新會員資料
Route::post('/dashboard/update_userdata', 'Frontend\DashboardController@updateUserData')
       ->name('dashboard.update.userdata')
       ->middleware('auth');

// 執行登出工作階段
Route::get('/dashboard/logoutsession/{sid}', 'Frontend\DashboardController@logoutSession')
       ->name('dashboard.logout-session')
       ->middleware('auth');

// 登入/註冊頁面
Route::get('/useraction', 'Auth\LoginController@showForm')->name('useraction');

// 執行登入
Route::post('/login', 'Auth\LoginController@login')->name('login');

// 執行登出
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// 執行註冊
Route::post('/register', 'Auth\RegisterController@register')->name('register');