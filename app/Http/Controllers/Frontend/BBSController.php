<?php

namespace App\Http\Controllers\Frontend;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Models\BBSBoard;
use App\Models\BBSPost;
use App\Models\BBSArticle;
use App\Models\GlobalSettings;

class BBSController extends Controller
{
    public function index()
    {
        // 取得目前未被隱藏的所有討論板
        $boards = BBSBoard::where('boardHide', 0)->orderBy('boardID', 'ASC')->get();
        // 處理麵包屑資訊
        $bc = [
            ['url' => route('boardselect'), 'name' => '選擇討論板']
        ];
        // 返回資料給 blade
        return view('frontend.bbsboard', compact('boards', 'bc'));
    }

    public function show(Request $request, $bid)
    {
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page = $request->query('p');
        }else{
            $page = 1;
        }
        // 如果討論板 ID 是空的就直接踢回選擇討論板頁面
        if(empty($bid)){
            return redirect(route('boardselect'));
        }
        // 查資料庫取目前討論板的基礎資訊
        $boardInfo = BBSBoard::where('boardID', $bid);
        // 討論板不存在，返回選擇討論板頁面
        if($boardInfo->count() == 0){
            return redirect(route('boardselect'));
        }
        // 討論板存在，取得該筆資料
        $boardInfo = $boardInfo->first();
        // 取得一頁顯示多少討論串
        $dispNums = GlobalSettings::where('settingName', 'postsNum')->value('settingValue');
        // 討論串數量
        $postNums = BBSPost::where('postBoard', $bid)->count();
        // 計算總頁數
        $tpage = ceil($postNums / $dispNums);
        // 起始頁
        $start = $dispNums * ($page - 1);
        // 取得目前討論板的討論串
        $boardcontents = BBSPost::where('postBoard', $bid)->skip($start)->take($dispNums)->orderBy('lastUpdateTime', 'DESC')->get();
        // 先建立儲存回文數的陣列，然後在 foreach 每個貼文 ID 取得回文數量，然後再存回 $articleNums 中。
        // 其中回文的查詢使用 Left Join 結合貼文和回文成一個大表格。
        $articleNums = array();
        foreach($boardcontents as $post){
            array_push($articleNums, DB::table('bbspost')->leftjoin('bbsarticle', 'bbsarticle.articlePost', '=', 'bbspost.postID')->where('bbspost.postID', $post['postID'])->count());
        }
        // 判斷為熱門回應的閥值
        $hotpost = 100;
        // 處理麵包屑資訊
        $bc = [
            ['url' => route('boardselect'), 'name' => '討論專區'],
            ['url' => route(Route::currentRouteName(), ['bid' => $bid]), 'name' => $boardInfo->boardName]
        ];
        // 返回資料給 blade
        return view('frontend.bbsshowboard', compact('boardcontents', 'boardInfo', 'bc', 'bid', 'postNums', 'articleNums', 'hotpost', 'page', 'tpage'));
    }

    public function view(Request $request, $bid, $postid)
    {
        // 檢視討論串後端程式
    }
}
