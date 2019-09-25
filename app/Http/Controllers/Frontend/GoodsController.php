<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\GlobalSettings;

class GoodsController extends Controller
{
    public function index($page = 1)
    {
        // 目前頁數
        $cPage = $page;
        // 設定每頁顯示幾行
        $goodsNum = GlobalSettings::where('settingName', 'goodsNum')->value('settingValue');
        // 計算頁數
        $tPage = ceil(Goods::count() / $goodsNum);
        // 取得特定頁面商品
        // 先以頁數算起始值
        $start = $goodsNum * ($page - 1);
        /**
         * 取資料
         * skip 是 SQL 中 LIMIT a, b 中的 a 值
         * take 是 SQL 中 LIMIT a, b 中的 b 值
         */
        $goods = Goods::skip($start)->take($goodsNum)->get();
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽']
        ];
        return view('frontend.goods', compact('goods', 'cPage', 'tPage', 'bc'));
    }

    public function show($goodId)
    {
        // 取得商品數量顏色區分的閥值
        $goodQtyDanger = GlobalSettings::where('settingName', 'goodQtyDanger')->value('settingValue');
        // 取得目標商品的資料列
        $goodData = Goods::where('goodsOrder', $goodId)->first();
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽'],
            ['url' => route(Route::currentRouteName(), ['id' => $goodId]), 'name' => $goodData->goodsName]
        ];
        return view('frontend.gooddetail', compact('goodData', 'goodQtyDanger', 'bc'));
    }
}
