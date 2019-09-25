<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\GlobalSettings;
use Illuminate\Support\Facades\Route;

class NewsController extends Controller
{
    public function index($page = 1)
    {
        $cPage = $page;
        // 取得單頁顯示行數
        $newsQty = GlobalSettings::where('settingName', 'newsNum')->value('settingValue');
        // 計算總頁數
        $tPage = ceil(News::count() / $newsQty);
        // 計算起始值
        $start = ($cPage - 1) * $newsQty;
        // 取得消息
        $newsData = News::skip($start)->take($newsQty)->orderBy('newsOrder', 'DESC')->get();
        $bc = [
            ['url' => route('news'), 'name' => '最新消息一覽']
        ];
        return view('frontend.news', compact('newsData', 'cPage', 'tPage', 'bc'));
    }

    public function show($id)
    {
        $newsData = News::where('newsOrder', $id)->first();
        $bc = [
            ['url' => route('news'), 'name' => '最新消息一覽'],
            ['url' => route(Route::currentRouteName(), ['id' => $id]), 'name' => $newsData->newsTitle]
        ];
        return view('frontend.newsdetail', compact('newsData', 'bc'));
    }
}
