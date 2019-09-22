<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\GlobalSettings;

class GoodsController extends Controller
{
    public function index($page = 1)
    {
        $page = $page;
        // 設定每頁顯示幾行
        $gSettings = GlobalSettings::select('settingValue AS goodsNum')->where('settingName', 'goodsNum')->first();
        // 取得特定頁面商品
        // 先以頁數算起始值
        $start = $gSettings->goodsNum * ($page - 1);
        /**
         * 取資料
         * skip 是 SQL 中 LIMIT a, b 中的 a 值
         * take 是 SQL 中 LIMIT a, b 中的 b 值
         */
        $goods = Goods::skip($start)->take($gSettings->goodsNum)->get();
        return view('frontend.goods', compact('goods'));
    }
}
