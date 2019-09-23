<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\GlobalSettings;

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
        return view('frontend.news', compact('newsData', 'cPage', 'tPage'));
    }

    public function show($id)
    {
        $newsData = News::where('newsOrder', $id)->first();
        return view('frontend.newsdetail', compact('newsData'));
    }
}
