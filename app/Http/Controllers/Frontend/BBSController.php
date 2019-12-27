<?php

namespace App\Http\Controllers\Frontend;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\BBSBoard;
use App\Models\BBSPost;
use App\Models\BBSArticle;
use App\Models\GlobalSettings;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Notifications;
use Exception;
use Carbon\Carbon;

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
        // 檢查討論板是否存在
        $boardinfo = $this->checkIDs('board', $bid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($boardinfo)){
            return $boardinfo;
        }
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page['this'] = $request->query('p');
        }else{
            $page['this'] = 1;
        }
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
            array_push($articleNums, BBSArticle::where('articlePost', $post['postID'])->where('articleStatus', '!=', '4')->count());
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
        // 檢查討論板及討論串是否存在
        $basicInfo = $this->checkIDs('both', $bid, $postid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($basicInfo)){
            return $basicInfo;
        }else{
            $boardinfo = $basicInfo['boardinfo'];
            $postinfo = $basicInfo['postinfo'];
        }
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page['this'] = $request->query('p');
        }else{
            $page['this'] = 1;
        }
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
            $userData = User::select('username', 'userNickname', 'userAvator')->whereIn('userName', $users)->get();
            // 最後把這些帳號和暱稱處理成一個陣列就完成了
            $username = array();
            foreach($userData as $ud){
                $username[$ud['username']] = [
                    'nickname' => $ud['userNickname'], 
                    'avator' => $ud['userAvator']
                ];
            }
        }
        // 檢查文章狀態，被鎖定或被刪除要給出錯誤訊息，先檢查是不是被鎖定
        if(in_array($postinfo['status'], [2, 3])){
            $error = [
                'msg' => '此文章已被鎖定，不可以新增任何回覆！',
                'type' => 'warning',
            ];
        }
        // 如果是被刪除
        elseif($postinfo['status'] == 4){
            // 有登入且是板主
            if(Auth::check() && Auth::user()->userPriviledge >= $boardinfo['adminPriv']){
                $error = [
                    'msg' => '此文章已被刪除，只有板主和管理員可以檢視此文章，且不可以新增任何回覆！',
                    'type' => 'warning',
                ];
            }
            // 否則就跳回討論板
            else{
                return redirect(route('showboard', ['bid'=> $bid]))
                       ->withErrors([
                           'msg'=> '該文章已被刪除',
                           'type'=> 'error',
                       ]);
            }
        }
        // 處理麵包屑資訊
        $bc = [
            ['url' => route('boardselect'), 'name' => '討論專區'],
            ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
            ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid', $postid]), 'name' => $postinfo['title']],
        ];
        if(empty($error)){
            return view('frontend.bbs.bbsshowpost', compact('bc', 'boardinfo', 'postinfo', 'postDatas', 'username', 'page'));
        }else{
            return view('frontend.bbs.bbsshowpost', compact('bc', 'boardinfo', 'postinfo', 'postDatas', 'username', 'page'))
                   ->withErrors($error);
        }
    }

    /**
     * 顯示建立討論串表單
     * @param \Illuminate\Http\Request  $request Request 實例
     * @param Request $request
     * @param int $bid 討論板 ID
     * @return view
     */
    public function showCreatePostForm(Request $request, $bid)
    {
        // 先檢查是否被禁言
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可發表新文章',
                'type'=> 'error',
            ]);
        }
        // 檢查討論板是否存在
        $boardinfo = $this->checkIDs('board', $bid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($boardinfo)){
            return $boardinfo;
        }
        $bc = [
            ['url' => route('boardselect'), 'name' => '討論專區'],
            ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
            ['url' => route(Route::currentRouteName(), ['bid' => $bid]), 'name' => '張貼新文章'],
        ];
        return view('frontend.bbs.bbscreatepost', compact('bc', 'boardinfo'));
    }

    /**
     * 執行建立討論串
     */
    public function createPost(Request $request, $bid)
    {
        // 先檢查是否被禁言
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可發表新文章',
                'type'=> 'error',
            ]);
        }
        // 檢查討論板是否存在
        $boardinfo = $this->checkIDs('board', $bid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($boardinfo)){
            return $boardinfo;
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'posttitle' => ['required', 'string', 'max:100'],
            'posttype' => ['required', 'string'],
            'postcontent' => ['required', 'string', 'max:5000'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 驗證成功
        else
        {
            // 寫入資料庫
            $post = BBSPost::create([
                'postTitle' => $request->input('posttitle'),
                'postType' => $request->input('posttype'),
                'postContent' => $request->input('postcontent'),
                'postUserID' => Auth::user()->userName,
                'postBoard' => $boardinfo['id'],
            ]);
            // 取得新文章的 ID
            $newPostID = $post->postID;
            // 直接跳轉至新文章
            return redirect(route('viewdiscussion', ['bid' => $bid, 'postid' => $newPostID]))
                   ->withErrors([
                       'msg' => '張貼新文章成功！',
                       'type' => 'success',
                   ]);
        }
    }

    /**
     * 顯示回覆討論串表單
     * @param \Illuminate\Http\Request $request Request 實例
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @return view
     */
    public function showReplyPostForm(Request $request, $bid, $postid)
    {
        // 先檢查是否被禁言
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可發表回文',
                'type'=> 'error',
            ]);
        }
        // 檢查討論板及討論串是否存在
        $basicInfo = $this->checkIDs('both', $bid, $postid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($basicInfo)){
            return $basicInfo;
        }else{
            $boardinfo = $basicInfo['boardinfo'];
            $postinfo = $basicInfo['postinfo'];
        }
        // 檢查文章狀態，被鎖定或被刪除要重導並給出錯誤訊息
        if(in_array($postinfo['status'], [2, 3, 4])){
            return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                   ->withErrors([
                       'msg' => '此文章已被鎖定或刪除，不可以新增任何回覆！',
                       'type' => 'error',
                   ]);
        }
        $bc = [
            ['url' => route('boardselect'), 'name' => '討論專區'],
            ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
            ['url' => route('viewdiscussion', ['bid' => $bid, 'postid' => $postid]), 'name' => $postinfo['title']],
            ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid' => $postid]), 'name' => '回覆文章'],
        ];
        return view('frontend.bbs.bbsreplypost', compact('bc', 'boardinfo', 'postinfo'));
    }

    /**
     * 執行張貼回文
     * @param \Illuminate\Http\Request $request Request 實例
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @return redirect Redirect 實例
     */
    public function replyPost(Request $request, $bid, $postid)
    {
        // 先檢查是否被禁言
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可發表回文',
                'type'=> 'error',
            ]);
        }
        // 檢查討論板及討論串是否存在
        $basicInfo = $this->checkIDs('both', $bid, $postid);
        // 返回的結果不是陣列就是 redirectResponse
        if(!is_array($basicInfo)){
            return $basicInfo;
        }else{
            $boardinfo = $basicInfo['boardinfo'];
            $postinfo = $basicInfo['postinfo'];
        }
        // 檢查文章狀態，被鎖定或被刪除要重導並給出錯誤訊息
        if(in_array($postinfo['status'], [2, 3, 4])){
            return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                   ->withErrors([
                       'msg' => '此文章已被鎖定或刪除，不可以新增任何回覆！',
                       'type' => 'error',
                   ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'replytitle' => ['nullable', 'string', 'max:100'],
            'replycontent' => ['required', 'string', 'max:5000'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 驗證成功
        else
        {
            // 寫入資料庫
            BBSArticle::create([
                'articleTitle' => $request->input('replytitle'),
                'articleContent' => $request->input('replycontent'),
                'articleUserID' => Auth::user()->userName,
                'articlePost' => $postinfo['id'],
            ]);
            BBSPost::where('postID', $postinfo['id'])->update([
                'lastUpdateUserID' => Auth::user()->userName,
                'lastUpdateTime' => Carbon::now(),
            ]);
            // 給予主貼文發文者通知
            $notifytext = '文章「' . $postinfo['title'] . '」已有新回覆！';
            $this->notifyuser('新回文通知', $notifytext, $boardinfo, $postinfo);
            // 由於有載入動畫的緣故，不實作跳轉至最新回文
            // 總回文數
            $replyNums = BBSArticle::where('articlePost', $postinfo['id'])->count();
            // 每頁顯示回文數
            $dispNums = GlobalSettings::where('settingName', 'articlesNum')->value('settingValue');
            // 計算跳轉的頁碼
            $referPage = ceil($replyNums / $dispNums);
            /* // 計算跳轉後的 ID
            $referAID = $replyNums % $dispNums;
            $request->session()->flash('scrollToPosition', $referAID); */

            // 直接跳轉至新回文
            return redirect(route('viewdiscussion', ['bid' => $bid, 'postid' => $postinfo['id'], 'p=' . $referPage]))
                   ->withErrors([
                       'msg' => '回覆文章成功！',
                       'type' => 'success',
                   ]);
        }

    }
    
    /**
     * 編輯貼文
     * 
     * @param Request $request
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @param string $type (post|reply|null) 主貼文或回文
     * @param int $targetpost (int|null) 編輯對象貼文的 ID
     * @return view
     */
    public function editPost(Request $request, $bid, $postid, $type = null, $targetpost = null)
    {
        // 先檢查是否被禁言或沒登入
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可編輯內文',
                'type'=> 'error',
            ]);
        }
        // 再判斷是編輯主文章還是回文
        switch($type){
            case 'post':
            case empty($type):
            case null:
                $idType = 'postid';
                break;
            case 'reply':
                $idType = 'replyid';
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序編輯文章',
                           'type' => 'error',
                       ]);
        }
        // 如果 $targetpost 變數為空表示編輯的對象是主貼文
        if(empty($targetpost) || $targetpost === null){
            $targetpost = $postid;
        }
        // 先檢查貼文 ID 和討論板 ID（跳轉用）
        $referData = $this->checkIDs('both', $bid, $postid);
        if(!is_array($referData)){
            return $referData;
        }else{
            $boardinfo = $referData['boardinfo'];
            $postinfo = $referData['postinfo'];
        }
        // 主文章上面就檢查過了
        if($type == 'reply'){
            // 再檢查要編輯的對象是否存在
            $targetpostinfo = $this->checkIDs($idType, $bid, $targetpost);
            // 返回的結果不是陣列就是 redirectResponse
            if(!is_array($postinfo)){
                return $targetpostinfo;
            }
        }
        // 檢查文章狀態，被鎖定或被刪除要重導並給出錯誤訊息
        if(in_array($postinfo['status'], [2, 3, 4])){
            return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                   ->withErrors([
                       'msg' => '此文章已被鎖定或刪除，不可以執行編輯！',
                       'type' => 'error',
                   ]);
        }
        // 沒有跳轉就開始處理顯示項目
        switch($idType){
            case 'postid':
                // 拿文章資料
                $postData = [
                    'type'=> 'post',
                    'id'=> $targetpost,
                    'data'=> BBSPost::where('postID', $targetpost)->first(),
                ];
                // 最後檢查編輯的對象是不是由這支帳號發出的
                if($postData['data']->postUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以編輯他人文章',
                               'type' => 'error',
                           ]);
                }
                $bc = [
                    ['url' => route('boardselect'), 'name' => '討論專區'],
                    ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
                    ['url' => route('viewdiscussion', ['bid' => $bid, 'postid' => $postid]), 'name' => $postinfo['title']],
                    ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid' => $postid, 'type' => $type, 'targetpost' => $targetpost]), 'name' => '編輯文章'],
                ];
                return view('frontend.bbs.bbseditpost', compact('bc', 'boardinfo', 'postinfo', 'postData'));
                break;
            case 'replyid':
                // 拿回文資料
                $postData = [
                    'type'=> 'reply',
                    'id'=> $targetpost,
                    'data'=> BBSArticle::where('articleID', $targetpost)->first(),
                ];
                // 最後檢查編輯的對象是不是由這支帳號發出的
                if($postData['data']->articleUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以編輯他人文章',
                               'type' => 'error',
                           ]);
                }
                $bc = [
                    ['url' => route('boardselect'), 'name' => '討論專區'],
                    ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
                    ['url' => route('viewdiscussion', ['bid' => $bid, 'postid' => $postid]), 'name' => $postinfo['title']],
                    ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid' => $postid, 'type' => $type, 'targetpost' => $targetpost]), 'name' => '編輯文章'],
                ];
                return view('frontend.bbs.bbseditpost', compact('bc', 'boardinfo', 'postinfo','postData'));
                break;
            default:
                return back()
                ->withErrors([
                    'msg' => '請依正常程序編輯文章',
                    'type' => 'error',
                ]);
        }
    }

    /**
     * 執行編輯貼文
     * 
     * @param Request $request Request 實例
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @param string $type (post|reply|null) 主貼文或回文
     * @param int $targetpost (int|null) 編輯對象貼文的 ID
     * @return redirect Redirect 實例
     */
    public function doEditPost(Request $request, $bid, $postid, $type = null, $targetpost = null)
    {
        // 先檢查是否被禁言
        if(Auth::user()->userPriviledge == 1){
            return back()->withErrors([
                'msg'=> '您處於被禁言狀態，不可編輯內文',
                'type'=> 'error',
            ]);
        }
        // 再判斷是編輯主文章還是回文
        switch($type){
            case 'post':
            case empty($type):
            case null:
                $idType = 'postid';
                break;
            case 'reply':
                $idType = 'replyid';
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '無法正確判斷文章類型，請依正常程序編輯貼文！',
                           'type' => 'error',
                       ]);
        }
        // 如果 $targetpost 變數為空表示編輯的對象是主貼文
        if(empty($targetpost) || $targetpost === null){
            $targetpost = $postid;
        }
        // 先檢查貼文 ID 和討論板 ID（跳轉用）
        $referData = $this->checkIDs('both', $bid, $postid);
        if(!is_array($referData)){
            return $referData;
        }else{
            $boardinfo = $referData['boardinfo'];
            $postinfo = $referData['postinfo'];
        }
        // 主文章上面就檢查過了
        if($type == 'reply'){
            // 再檢查要編輯的對象是否存在
            $targetpostinfo = $this->checkIDs($idType, $bid, $targetpost);
            // 返回的結果不是陣列就是 redirectResponse
            if(!is_array($postinfo)){
                return $targetpostinfo;
            }
        }
        // 檢查文章狀態，被鎖定或被刪除要重導並給出錯誤訊息
        if(in_array($postinfo['status'], [2, 3, 4])){
            return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                   ->withErrors([
                       'msg' => '此文章已被鎖定或刪除，不可以執行編輯！',
                       'type' => 'error',
                   ]);
        }
        // 沒有跳轉就開始處理顯示項目
        switch($idType){
            case 'postid':
                // 拿文章資料
                $qPostData = BBSPost::where('postID', $targetpost);
                $postData = [
                    'type'=> 'post',
                    'id'=> $targetpost,
                    'data'=> $qPostData->first(),
                ];
                // 檢查編輯的對象是不是由這支帳號發出的
                if($postData['data']->postUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以編輯他人文章',
                               'type' => 'error',
                           ]);
                }
                // 最後檢查文章有沒有被鎖定
                elseif(in_array($postData['data']->postStatus, [2, 3])){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '此文章已被鎖定，不可編輯此文章',
                               'type' => 'error',
                           ]);
                }
                // 驗證表單資料
                $validator = Validator::make($request->all(), [
                    'posttitle' => ['required', 'string', 'max:100'],
                    'posttype' => ['required', 'string'],
                    'postcontent' => ['required', 'string', 'max:5000'],
                ]);
                // 若驗證失敗
                if ($validator->fails()) {
                    // 針對錯誤訊息新增一欄訊息類別
                    $validator->errors()->add('type', 'error');
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }elseif($request->input('posttype') == '板務公告' && Auth::user()->userPriviledge < $boardinfo['adminPriv']){
                    return back()
                        ->withInput()
                        ->withErrors([
                            'msg'=> '權限不足不可設定此分類',
                            'type'=> 'error',
                        ]);
                }
                // 沒有跳轉就寫入資料庫
                $qPostData->update([
                    'postTitle' => $request->input('posttitle'),
                    'postType' => $request->input('posttype'),
                    'postContent' => $request->input('postcontent'),
                    'lastUpdateUserID' => Auth::user()->userName,
                    'lastUpdateTime' => Carbon::now(),
                    'postStatus' => 1,
                    'postEdittime' => Carbon::now(),
                ]);
                // 給予主貼文發文者通知
                $notifytext = '文章「' . $postinfo['title'] . '」已被編輯！';
                $this->notifyuser('文章被編輯通知', $notifytext, $boardinfo, $postinfo);
                return redirect(route('viewdiscussion', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id']]))
                       ->withErrors([
                           'msg'=> '編輯文章成功',
                           'type'=> 'success',
                       ]);
                break;
            case 'replyid':
                // 拿回文資料
                $qArticleData = BBSArticle::where('articleID', $targetpost);
                $postData = [
                    'type'=> 'reply',
                    'id'=> $targetpost,
                    'data'=> $qArticleData->first(),
                ];
                // 檢查編輯的對象是不是由這支帳號發出的
                if($postData['data']->articleUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以編輯他人文章',
                               'type' => 'error',
                           ]);
                }
                // 最後檢查文章有沒有被鎖定
                elseif(in_array($postData['data']->articleStatus, [2, 3])){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '此文章已被鎖定，不可編輯此文章',
                               'type' => 'error',
                           ]);
                }
                // 驗證表單資料
                $validator = Validator::make($request->all(), [
                    'replytitle' => ['nullable', 'string', 'max:100'],
                    'replycontent' => ['required', 'string', 'max:5000'],
                ]);
                // 若驗證失敗
                if ($validator->fails()) {
                    // 針對錯誤訊息新增一欄訊息類別
                    $validator->errors()->add('type', 'error');
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }
                // 沒有跳轉就寫入資料庫
                $qArticleData->update([
                    'articleTitle' => $request->input('replytitle'),
                    'articleContent' => $request->input('replycontent'),
                    'articleStatus' => 1,
                    'articleEdittime' => Carbon::now(),
                ]);
                BBSPost::where('postID', $postinfo['id'])
                         ->update([
                             'lastUpdateUserID' => Auth::user()->userName,
                             'lastUpdateTime' => Carbon::now(),
                         ]);
                // 寫完資料庫開始準備跳轉，如果目標不是編輯文章的頁面就跳轉過去
                if($request->input('refer') != route('bbs.showeditpostform', [
                    'bid'=> $bid, 'postid'=> $postid, 'type'=> $type, 'targetpost'=> $targetpost
                ])){
                    $path = $request->input('refer');
                }else{
                    // 由於有載入動畫的緣故，不實作跳轉至最新回文
                    // 總回文數
                    $replyNums = BBSArticle::where('articlePost', $postinfo['id'])->count();
                    // 每頁顯示回文數
                    $dispNums = GlobalSettings::where('settingName', 'articlesNum')->value('settingValue');
                    // 計算跳轉的頁碼
                    $referPage = ceil($replyNums / $dispNums);
                    $path = route('viewdiscussion', ['bid' => $bid, 'postid' => $postinfo['id'], 'p=' . $referPage]);
                }
                return redirect($path)
                       ->withErrors([
                            'msg' => '編輯回文成功！',
                            'type' => 'success',
                       ]);
                break;
            default:
                return back()
                ->withErrors([
                    'msg' => '無法正確判斷文章類型，請依正常程序編輯貼文！',
                    'type' => 'error',
                ]);
        }
    }


    /**
     * 顯示刪除文章確認表單
     * @param Request $request Request 實例
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @param string $type (post|reply|null) 主貼文或回文
     * @param int $targetpost (int|null) 編輯對象貼文的 ID
     * @return view 視圖
     */
    public function showDeleteConfirmForm(Request $request, $bid, $postid, $type = null, $targetpost = null)
    {
        // 先判斷是編輯主文章還是回文
        switch($type){
            case 'post':
            case empty($type):
            case null:
                $idType = 'postid';
                break;
            case 'reply':
                $idType = 'replyid';
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序刪除文章',
                           'type' => 'error',
                       ]);
        }
        // 如果 $targetpost 變數為空表示編輯的對象是主貼文
        if(empty($targetpost) || $targetpost === null){
            $targetpost = $postid;
        }
        // 先檢查貼文 ID 和討論板 ID（跳轉用）
        $referData = $this->checkIDs('both', $bid, $postid);
        if(!is_array($referData)){
            return $referData;
        }else{
            $boardinfo = $referData['boardinfo'];
            $postinfo = $referData['postinfo'];
        }
        // 主文章上面就檢查過了
        if($type == 'reply'){
            // 再檢查要編輯的對象是否存在
            $targetpostinfo = $this->checkIDs($idType, $bid, $targetpost);
            // 返回的結果不是陣列就是 redirectResponse
            if(!is_array($postinfo)){
                return $targetpostinfo;
            }
        }
        // 檢查文章狀態，被鎖定或被刪除要重導並給出錯誤訊息
        if(in_array($postinfo['status'], [4])){
            return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                   ->withErrors([
                       'msg' => '此文章已經被刪除！',
                       'type' => 'error',
                   ]);
        }
        // 沒有跳轉就開始處理顯示項目
        switch($idType){
            case 'postid':
                // 拿文章資料
                $postData = [
                    'type'=> 'post',
                    'id'=> $targetpost,
                    'data'=> BBSPost::where('postID', $targetpost)->first(),
                ];
                // 最後檢查權限是不是板主或編輯的對象是不是由這支帳號發出的
                if(Auth::user()->userPriviledge < $boardinfo['adminPriv'] && $postData['data']->postUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以刪除別人的文章',
                               'type' => 'error',
                           ]);
                }
                $postData['usernickname'] = User::where('userName', $postData['data']->postUserID)->value('userNickname');
                $bc = [
                    ['url' => route('boardselect'), 'name' => '討論專區'],
                    ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
                    ['url' => route('viewdiscussion', ['bid' => $bid, 'postid' => $postid]), 'name' => $postinfo['title']],
                    ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid' => $postid, 'type' => $type, 'targetpost' => $targetpost]), 'name' => '確認刪除文章'],
                ];
                return view('frontend.bbs.bbsdelpost', compact('bc', 'boardinfo', 'postinfo', 'postData'));
                break;
            case 'replyid':
                // 拿文章資料
                $postData = [
                    'type'=> 'reply',
                    'id'=> $targetpost,
                    'data'=> BBSArticle::where('articleID', $targetpost)->first(),
                ];
                // 最後檢查權限是不是板主或編輯的對象是不是由這支帳號發出的
                if(Auth::user()->userPriviledge < $boardinfo['adminPriv'] && $postData['data']->articleUserID != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以刪除別人的文章',
                               'type' => 'error',
                           ]);
                }
                $postData['usernickname'] = User::where('userName', $postData['data']->articleUserID)->value('userNickname');
                $bc = [
                    ['url' => route('boardselect'), 'name' => '討論專區'],
                    ['url' => route('showboard', ['bid' => $bid]), 'name' => $boardinfo['name']],
                    ['url' => route('viewdiscussion', ['bid' => $bid, 'postid' => $postid]), 'name' => $postinfo['title']],
                    ['url' => route(Route::currentRouteName(), ['bid' => $bid, 'postid' => $postid, 'type' => $type, 'targetpost' => $targetpost]), 'name' => '確認刪除回文'],
                ];
                return view('frontend.bbs.bbsdelpost', compact('bc', 'boardinfo', 'postinfo', 'postData'));
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序刪除文章',
                           'type' => 'error',
                       ]);
        }
    }

    /**
     * 執行刪除文章
     * @param Request $request Request 實例
     * @param int $bid 討論板 ID
     * @param int $postid 討論串 ID
     * @param string $type (post|reply|null) 主貼文或回文
     * @param int $targetpost (int|null) 編輯對象貼文的 ID
     * @return redirect Redirect 實例
     */
    public function deletePost(Request $request, $bid, $postid, $type = null, $targetpost = null)
    {
        // 先判斷是編輯主文章還是回文
        switch($type){
            case 'post':
            case empty($type):
            case null:
                $idType = 'postid';
                break;
            case 'reply':
                $idType = 'replyid';
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序刪除文章',
                           'type' => 'error',
                       ]);
        }
        // 如果 $targetpost 變數為空表示編輯的對象是主貼文
        if(empty($targetpost) || $targetpost === null){
            $targetpost = $postid;
        }
        // 先檢查貼文 ID 和討論板 ID（跳轉用）
        $referData = $this->checkIDs('both', $bid, $postid);
        if(!is_array($referData)){
            return $referData;
        }else{
            $boardinfo = $referData['boardinfo'];
            $postinfo = $referData['postinfo'];
        }
        // 主文章上面就檢查過了
        if($type == 'reply'){
            // 再檢查要編輯的對象是否存在
            $targetpostinfo = $this->checkIDs($idType, $bid, $targetpost);
            // 返回的結果不是陣列就是 redirectResponse
            if(!is_array($postinfo)){
                return $targetpostinfo;
            }
        }
        // 沒有跳轉就開始處理顯示項目
        switch($idType){
            case 'postid':
                // 最後檢查權限是不是板主或編輯的對象是不是由這支帳號發出的
                $postData = BBSPost::where('postID', $targetpost);
                if(Auth::user()->userPriviledge < $boardinfo['adminPriv'] && $postData->value('postUserID') != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以刪除別人的文章',
                               'type' => 'error',
                           ]);
                }
                // 沒問題就開始更新資料庫，目前決定以軟刪除方式刪除文章
                // 設定主貼文
                $postData->update([
                    'postStatus'=> 4,
                ]);
                // 其底下的回文
                BBSArticle::where('articlePost', $targetpost)->update([
                    'articleStatus'=> 4,
                ]);
                return redirect(route('showboard', ['bid'=> $bid]))
                       ->withErrors([
                           'msg' => '刪除文章成功',
                           'type' => 'success',
                       ]);
                break;
            case 'replyid':
                // 最後檢查權限是不是板主或編輯的對象是不是由這支帳號發出的
                $postData = BBSArticle::where('articleID', $targetpost);
                if(Auth::user()->userPriviledge < $boardinfo['adminPriv'] && $postData->value('articleUserID') != Auth::user()->userName){
                    return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid' => $postid]))
                           ->withErrors([
                               'msg' => '不可以刪除別人的文章',
                               'type' => 'error',
                           ]);
                }
                // 沒問題就開始更新資料庫，目前決定以軟刪除方式刪除文章
                // 該則回文
                $postData->update([
                    'articleStatus'=> 4,
                ]);
                BBSPost::where('postID', $postid)->update([
                    'lastUpdateUserID'=> Auth::user()->userName,
                    'lastUpdateTime'=> Carbon::now(),
                ]);
                return redirect(route('viewdiscussion', ['bid'=> $bid, 'postid'=> $postid]))
                       ->withErrors([
                           'msg' => '刪除回文成功',
                           'type' => 'success',
                       ]);
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序刪除文章',
                           'type' => 'error',
                       ]);
        }
    }

    /**
     * 檢查討論板 ID 和討論串 ID 是否存在
     * @param string $type (board|both) 僅檢查討論板 ID 或兩者都檢查
     * @param int $boardid 討論板 ID
     * @param int $postid 討論串 ID
     * @return mixed
     */
    public function checkIDs($type = 'board', $boardid = null, $postid = null)
    {
        // 查資料庫取目前討論板的基礎資訊
        $boardInfo = BBSBoard::where('boardID', $boardid);
        // 取得管理討論板權限等級
        $boardAdmin = GlobalSettings::where('settingName', 'adminPriv')->value('settingValue');
        switch($type){
            case 'board':
                // 討論板 ID 或討論板不存在，返回選擇討論板頁面
                if(empty($boardid) || $boardInfo->count() == 0){
                    return redirect(route('boardselect'))->withErrors([
                        'msg' => '找不到該討論板',
                        'type' => 'error',
                    ]);
                }
                // 如果存在就把基礎資訊處理起來
                return [
                    'id' => $boardid,
                    'name' => $boardInfo->first()->boardName,
                    'adminPriv' => $boardAdmin,
                ];
                break;
            case 'postid':
                if(empty($postid)){
                    return back()->withErrors([
                        'msg' => '找不到該則貼文',
                        'type' => 'error',
                    ]);
                }else{
                    // 查資料庫取目前討論串的基礎資訊
                    $postInfo = BBSPost::where('postID', $postid);
                    // 討論串不存在，返回討論板頁面
                    if($postInfo->count() == 0){
                        return redirect(route('viewdiscussion', ['bid' => $boardid]))
                            ->withErrors([
                                'msg' => '找不到討論串',
                                'type' => 'error',
                            ]);
                    }
                    // 討論串存在就先處理標題和 ID
                    $pInfo = [
                        'id' => $postid,
                        'title' => $postInfo->first()->postTitle,
                        'status' => $postInfo->first()->postStatus,
                    ];
                    return $pInfo;
                }
                break;
            case 'replyid':
                if(empty($postid)){
                    return back()->withErrors([
                        'msg' => '找不到該則回文',
                        'type' => 'error',
                    ]);
                }else{
                    // 查資料庫取目前回文的基礎資訊
                    $postInfo = BBSArticle::where('articleID', $postid);
                    // 討論串不存在，返回討論板頁面
                    if($postInfo->count() == 0){
                        return redirect(route('showboard', ['bid' => $boardid]))
                            ->withErrors([
                                'msg' => '找不到該則回文',
                                'type' => 'error',
                            ]);
                    }
                    // 回文存在就先處理標題和 ID
                    $pInfo = [
                        'id' => $postid,
                        'title' => $postInfo->first()->articleTitle,
                        'status' => $postInfo->first()->articleStatus,
                    ];
                    return $pInfo;
                }
                break;
            case 'both':
                // 討論板 ID 或討論板不存在，返回選擇討論板頁面
                if(empty($boardid) || $boardInfo->count() == 0){
                    return redirect(route('boardselect'))->withErrors([
                        'msg' => '找不到該討論板',
                        'type' => 'error',
                    ]);
                }
                // 如果存在就把基礎資訊處理起來
                $bInfo = [
                    'id' => $boardid,
                    'name' => $boardInfo->first()->boardName,
                    'adminPriv' => $boardAdmin,
                ];
                // 查資料庫取目前討論串的基礎資訊
                $postInfo = BBSPost::where('postBoard', $boardid)->where('postID', $postid);
                // 討論串不存在，返回討論板頁面
                if($postInfo->count() == 0){
                    return redirect(route('showboard', ['bid' => $boardid]))
                           ->withErrors([
                               'msg' => '找不到討論串',
                               'type' => 'error',
                           ]);
                }
                // 討論串存在就先處理標題和 ID
                $pInfo = [
                    'id' => $postid,
                    'title' => $postInfo->first()->postTitle,
                    'status' => $postInfo->first()->postStatus,
                ];
                return [
                    'boardinfo' => $bInfo,
                    'postinfo' => $pInfo,
                ];
                break;
            default:
                throw new Exception('未知的類型');
        }
    }

    /**
     * 推送通知
     * @param string $notifytitle 通知標題
     * @param string $notifycontent 通知內容
     * @param array $boardinfo 討論板資訊陣列
     * @param array $postinfo 討論串資訊陣列
     */
    public function notifyuser(string $notifytitle, string $notifycontent, array $boardinfo, array $postinfo)
    {
        // 先取目前貼文所有的使用者（不重覆）
        $userRaw = BBSPost::find($postinfo['id'])->replies()->get('articleUserID');
        // 把使用者名稱放進陣列
        $users = [BBSPOST::where('postID', $postinfo['id'])->first()->postUserID];
        foreach($userRaw as $user){
            array_push($users, $user->articleUserID);
        }
        // 把重複的 ID 拿掉
        $users = array_unique($users);
        // 把自己的 ID 拿掉
        unset($users[array_search(Auth::user()->userName, $users)]);
        // 處理要放的訊息
        $notifies = [];
        foreach($users as $userid){
            array_push($notifies, [
                'notifyTitle'=> $notifytitle,
                'notifySource'=> '系統',
                'notifyContent'=> $notifycontent,
                'notifyTarget'=> $userid,
                'notifyURL'=> route('viewdiscussion', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id']]),
            ]);
        }
        Notifications::insert($notifies);
    }
}
