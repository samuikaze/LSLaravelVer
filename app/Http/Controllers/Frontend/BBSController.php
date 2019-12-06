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
use App\Models\Member;

class BBSController extends Controller
{
    /**
     * 選擇討論板
     * 
     * @return view frontend.bbs.bbsboard 並包含以下變數
     * @return $boards, $bc
     */
    public function index()
    {
        // 取得目前未被隱藏的所有討論板
        $boards = BBSBoard::where('boardHide', 0)->orderBy('boardID', 'ASC')->get();
        // 處理麵包屑資訊
        $bc = [
            ['url' => route('boardselect'), 'name' => '選擇討論板']
        ];
        // 返回資料給 blade
        return view('frontend.bbs.bbsboard', compact('boards', 'bc'));
    }

    /**
     * 顯示討論板內所有的討論串
     * 
     * @param \Illuminate\Http\Request  $request
     * @param int 討論板 ID
     * @return view frontend.bbs.bbsshowboard 並包含以下變數
     * @return $bc, $boardcontents, $boardinfo, $postNums, $articleNums, $hotpost, $page
     */
    public function show(Request $request, $bid)
    {
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page['this'] = $request->query('p');
        }else{
            $page['this'] = 1;
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
        // 如果存在就把基礎資訊處理起來
        $boardinfo = [
            'id' => $bid,
            'name' => $boardInfo->first()->boardName
        ];
        // 討論板存在，取得該筆資料
        $boardInfo = $boardInfo->first();
        // 取得一頁顯示多少討論串
        $dispNums = GlobalSettings::where('settingName', 'postsNum')->value('settingValue');
        // 討論串數量
        $postNums = BBSPost::where('postBoard', $bid)->count();
        // 計算總頁數
        $page['total'] = ceil($postNums / $dispNums);
        // 起始頁
        $start = $dispNums * ($page['this'] - 1);
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
            ['url' => route(Route::currentRouteName(), ['bid' => $bid]), 'name' => $boardinfo['name']]
        ];
        // 返回資料給 blade
        return view('frontend.bbs.bbsshowboard', compact('bc', 'boardcontents', 'boardinfo', 'postNums', 'articleNums', 'hotpost', 'page'));
    }

    /**
     * 檢視討論串
     * 
     * @param \Illuminate\Http\Request  $request
     * @param int 討論板 ID
     * @param int 討論串 ID
     * @return view frontend.bbs.bbsshowpost 並包含以下變數
     * @return $bc, $boardinfo, $postinfo, $postDatas, $username, $page
     */
    public function view(Request $request, $bid, $postid)
    {
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page['this'] = $request->query('p');
        }else{
            $page['this'] = 1;
        }
        // 如果討論串 ID 是空的就直接踢回討論串一覽頁面
        if(empty($postid)){
            return redirect(route('showboard', ['bid' => $bid]));
        }
        // 查資料庫取目前討論串的基礎資訊
        $postInfo = BBSPost::where('postID', $postid);
        // 討論板不存在，返回選擇討論板頁面
        if($postInfo->count() == 0){
            return redirect(route('showboard', ['bid' => $bid]));
        }
        // 討論串存在就先處理標題和 ID
        $postinfo = [
            'id' => $postid,
            'title' => $postInfo->first()->postTitle
        ];
        // 取得討論板資料
        $board = BBSBoard::where('boardID', $bid)->first();
        $boardinfo = [
            'id' => $bid,
            'name' => $board->boardName
        ];
        // 取得一頁顯示多少回文
        $postinfo['dispnums'] = GlobalSettings::where('settingName', 'articlesNum')->value('settingValue');
        // 回文數量
        $articleNums = BBSArticle::where('articlePost', $postid)->count();
        // 計算總頁數
        $page['total'] = ceil($articleNums / $postinfo['dispnums']);
        // 如果目前頁數比總頁數還多
        if($page['this'] != 1 && $page['this'] > $page['total']){
            $postinfo['exist'] = false;
            $postDatas = $username = null;
        }else{
            // 起始頁
            $start = $postinfo['dispnums'] * ($page['this'] - 1);
            // 如果討論串存在，取得該討論串所有貼文與回文
            // SELECT `bbspost`.*, `bbsarticle`.* FROM `bbspost` LEFT OUTER JOIN `bbsarticle` ON `bbsarticle`.`articlePost`=`bbspost`.`postID` WHERE `bbspost`.`postID`=1 AND `bbspost`.`postBoard`=1;
            $postDatas = DB::table('bbspost')->leftjoin('bbsarticle', 'bbsarticle.articlePost', '=', 'bbspost.postID')->select('bbspost.*', 'bbsarticle.*')->where('bbspost.postID', $postid)->where('bbspost.postBoard', $bid)->skip($start)->take($postinfo['dispnums']);
            $postDatas = $postDatas->get();
            $posttitle = $postDatas->first()->postTitle;
            $postinfo['exist'] = true;
            // 處理討論串中各會員的暱稱
            $users = array();
            // 先 foreach 每筆資料取出發文人的帳號
            foreach($postDatas as $i => $aData){
                if($i == 0){
                    if(!in_array($aData->postUserID, $users)){
                        array_push($users, $aData->postUserID);
                    }
                    if(!in_array($aData->articleUserID, $users)){
                        array_push($users, $aData->articleUserID);
                    }
                }else{
                    if(!in_array($aData->articleUserID, $users)){
                        array_push($users, $aData->articleUserID);
                    }
                }
            }
            // 再從資料庫取這些帳號的暱稱
            // SELECT * FROM `member` WHERE `userName` IN ('admin', 'user') ORDER BY `uid` ASC; 
            $userData = Member::select('username', 'userNickname', 'userAvator')->whereIn('userName', $users)->get();
            //最後把這些帳號和暱稱處理成一個陣列就完成了
            $username = array();
            foreach($userData as $ud){
                $username[$ud['username']] = [
                    'nickname' => $ud['userNickname'], 
                    'avator' => $ud['userAvator']
                ];
            }
        }
        // 處理麵包屑資訊
        $bc = [
            ['url' => route('boardselect'), 'name' => '討論專區'],
            ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
            ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid', $postid]), 'name' => $postinfo['title']],
        ];
        return view('frontend.bbs.bbsshowpost', compact('bc', 'boardinfo', 'postinfo', 'postDatas', 'username', 'page'));
    }
}
