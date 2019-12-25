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
Route::get('/goods', 'Frontend\GoodsController@viewgoods')->name('goods');

// 商品詳細
Route::get('/goods/goodDetail/{goodId}', 'Frontend\GoodsController@goodsdetail')->name('gooddetail');

// 檢視購物車
Route::get('/goods/viewcart', 'Frontend\GoodsController@viewcart')
       ->name('goods.viewcart')
       ->middleware('auth');

// 站內結帳
Route::match(['get', 'post'], 'goods/checkout/step/{step?}', 'Frontend\GoodsController@checkout')
       ->name('goods.checkout')
       ->middleware('auth');

// 執行取消結帳
Route::post('/goods/cancelcheckout', 'Frontend\GoodsController@cancelCheckout')
       ->name('goods.cancelcheckout')
       ->middleware('auth');

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

// 檢視訂單詳細資料
Route::get('/dashboard/orderdetail/{serial}', 'Frontend\DashboardController@orderDetail')
       ->name('dashboard.orderdetail')
       ->middleware('auth');

// 申請取消訂單表單
Route::get('/dashboard/removeorder/{serial}', 'Frontend\DashboardController@removeOrder')
       ->name('dashboard.removeorder')
       ->middleware('auth');

// 執行申請取消訂單
Route::post('/dashboard/removeorder/{serial}/apply', 'Frontend\DashboardController@doRemoveOrder')
       ->name('dashboard.dormorder')
       ->middleware('auth');

// 執行更新會員資料
Route::post('/dashboard/update_userdata', 'Frontend\DashboardController@updateUserData')
       ->name('dashboard.update.userdata')
       ->middleware('auth');

// 執行登出工作階段
Route::get('/dashboard/logoutsession/{sid}', 'Frontend\DashboardController@logoutSession')
       ->name('dashboard.logout-session')
       ->middleware('auth');

// 檢視通知頁面
Route::get('/notifications', 'Frontend\DashboardController@viewnotify')
       ->name('dashboard.viewnotify')
       ->middleware('auth');

// 登入/註冊頁面
Route::get('/useraction', 'Auth\LoginController@showForm')->name('useraction');

// 執行登入
Route::post('/login', 'Auth\LoginController@login')->name('login');

// 執行登出
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// 執行註冊
Route::post('/register', 'Auth\RegisterController@register')->name('register');

/**
 * AJAX 路由，每個都會經過 auth 這個 middleware 驗證登入狀態
 */
Route::middleware(['auth'])->group(function() {
    // 執行加入購物車
    Route::post('/ajax/goods/joincart', 'Frontend\GoodsController@joincart');

    // 執行變更購物車內商品數量
    Route::post('/goods/modifyqty', 'Frontend\GoodsController@modifyQty');

    // 執行重置購物車
    Route::post('/goods/resetcart', 'Frontend\GoodsController@resetcart')
        ->name('goods.resetcart');

    // 執行移除購物車項目
    Route::post('/goods/removeitem', 'Frontend\GoodsController@removeItem');

    // 執行儲存購物車
    Route::post('/goods/savecart', 'Frontend\GoodsController@savecart');

    // 執行通知已付款
    Route::post('/dashboard/notifypaid', 'Frontend\DashboardController@notifyPaid');

    // 執行通知已取貨
    Route::post('/dashboard/notifytaked', 'Frontend\DashboardController@notifyTaked');

    // 執行已讀（單則或全部）通知
    Route::post('/notifications/readnotify', 'Frontend\DashboardController@readNotify');

    // 執行移除（單則或全部）通知
    Route::post('/notifications/deletenotify', 'Frontend\DashboardController@deleteNotify');
});

/**
 * 後台路由，會經過「priv」這個 middleware 驗證存取權限，
 * 該 middleware 可視 kernel 下查閱，後台網址如下
 * ~/admin/{routeName}
 */
Route::prefix('admin')->middleware(['auth', 'priv'])->group(function() {
    // 後台首頁
    Route::get('/', 'Backend\HomeController@home')
           ->name('admin.index');

    // 輪播一覽與新增輪播
    Route::get('/carousel/a/{action}', 'Backend\Article\CarouselSettingController@carouselindex')
           ->name('admin.article.carousel');
    
    // 管理輪播
    Route::get('/carousel/editcarousel/{cid}', 'Backend\Article\CarouselSettingController@editCarousel')
           ->name('admin.article.editcarousel');

    // 刪除輪播確認表單
    Route::get('/carousel/deletecarousel/{cid}/confirm', 'Backend\Article\CarouselSettingController@confirmDelCarousel')
           ->name('admin.article.delcsconfirm');

    // 執行新增輪播
    Route::post('/carousel/addcarousel', 'Backend\Article\CarouselSettingController@addCarousel')
           ->name('admin.article.addcarousel');

    // 執行編輯輪播
    Route::post('/carousel/editcarousel/{cid}/edit', 'Backend\Article\CarouselSettingController@doEditCarousel')
           ->name('admin.article.doeditcs');

    // 執行刪除輪播
    Route::post('carousel/deletecarousel/{cid}/fire', 'Backend\Article\CarouselSettingController@fireDelCarousel')
           ->name('admin.article.dodeletecs');

    // 最新消息一覽與新增消息
    Route::get('/news/a/{action}', 'Backend\Article\NewsController@newsindex')
           ->name('admin.article.news');

    // 編輯消息表單
    Route::get('/news/editnews/{newsid}', 'Backend\Article\NewsController@editNews')
           ->name('admin.article.editnews');

    // 刪除消息確認表單
    Route::get('/news/deletenews/{newsid}/confirm', 'Backend\Article\NewsController@delNewsConfirm')
           ->name('admin.article.delnewsconfirm');
    
    // 執行編輯消息
    Route::post('/news/editnews/{newsid}/fire', 'Backend\Article\NewsController@fireEditNews')
           ->name('admin.article.doeditnews');

    // 執行張貼新消息
    Route::post('/news/addnews', 'Backend\Article\NewsController@addNews')
           ->name('admin.article.addnews');

    // 執行刪除消息
    Route::post('/news/deletenews/{newsid}/fire', 'Backend\Article\NewsController@fireDelNews')
           ->name('admin.article.firedelnews');

    // 作品一覽與新增作品
    Route::get('/product/a/{action}', 'Backend\Article\ProductController@productindex')
           ->name('admin.article.product');

    // 作品編輯表單
    Route::get('/product/editproduct/{pid}', 'Backend\Article\ProductController@editProduct')
           ->name('admin.article.editproduct');

    // 作品刪除確認表單
    Route::get('/product/deleteproduct/{pid}/confirm', 'Backend\Article\ProductController@delProdConfirm')
           ->name('admin.article.delprodconfirm');

    // 執行新增作品
    Route::post('/product/addproduct', 'Backend\Article\ProductController@addProduct')
           ->name('admin.article.addproduct');

    // 執行編輯作品
    Route::post('/product/editproduct/{pid}/fire', 'Backend\Article\ProductController@fireEditProduct')
           ->name('admin.article.doeditproduct');

    // 執行刪除作品
    Route::post('/product/deleteproduct/{pid}/fire', 'Backend\Article\ProductController@fireDelProduct')
           ->name('admin.article.firedelproduct');

    // 討論板管理
    Route::get('/bbs/a/{action}', 'Backend\BBS\BBSController@bbsindex')
           ->name('admin.bbs.bbs');

    // 編輯討論板表單
    Route::get('bbs/editboard/{bid}', 'Backend\BBS\BBSController@editBoard')
           ->name('admin.bbs.editboard');

    // 確認刪除討論板表單
    Route::get('bbs/deleteboard/{bid}/confirm', 'Backend\BBS\BBSController@delBoardConfirm')
           ->name('admin.bbs.delboardconfirm');

    // 執行新增討論板
    Route::post('bbs/createboard', 'Backend\BBS\BBSController@createBoard')
           ->name('admin.bbs.createboard');

    // 執行編輯討論板
    Route::post('bbs/editboard/{bid}/fire', 'Backend\BBS\BBSController@fireEditBoard')
           ->name('admin.bbs.doeditboard');

    // 執行刪除討論板
    Route::post('bbs/deleteboard/{bid}/fire', 'Backend\BBS\BBSController@fireDelBoard')
           ->name('admin.bbs.deleteboard');
});