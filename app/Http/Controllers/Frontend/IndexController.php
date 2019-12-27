<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Carousel;

class IndexController extends Controller
{
    /**
     * 顯示首頁
     * @return view 視圖
     */
    public function index()
    {
        $news = News::skip(0)->take(3)->orderBy('newsOrder', 'DESC')->get();
        $carouselCount = Carousel::count();
        $carousel = Carousel::orderBy('imgID', 'ASC')->get();
        return view('frontend.index', compact('news', 'carousel', 'carouselCount'));
    }

    /**
     * 當會員登入被停權的帳號時顯示的頁面
     * @return view 視圖
     */
    public function accountBanned()
    {
        // 如果是被停權才進頁面，否則踢回首頁
        if(Auth::user()->userPriviledge == 3){
            return view('frontend.accountbanned');
        }else{
            return redirect(route('index'));
        }
    }
}
