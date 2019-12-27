<?php

namespace App\Http\Controllers\Backend\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\BBSArticle;
use App\Models\GlobalSettings;
use App\Models\OrderDetail;
use App\Models\RemoveOrders;
use App\Models\User;
use App\Models\UserPriviledge;

class UserController extends Controller
{
    /**
     * 會員帳號管理
     * @param Request $request Request 實例
     * @param string $action 目前顯示頁面
     * @return view 視圖
     */
    public function userindex(Request $request, $action)
    {
        // 把 action 放進 info 陣列內
        $info['action'] = $action;
        // 拿目前頁數
        if(!empty($request->query('p'))){
            $info['thisPage'] = $request->query('p');
        }else{
            $info['thisPage'] = 1;
        }
        // 從資料庫取得一頁要顯示的數量
        $info['listnums'] = (int)GlobalSettings::where('settingName', 'adminListNum')->value('settingValue');
        // 先處理總頁數
        $info['nums'] = User::where('userPriviledge', '3')->count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得會員帳號
        $info['data'] = User::where('userPriviledge', '<>', '3')->skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('uid', 'asc')->get();
        $info['black'] = User::where('userPriviledge', '3')->skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('uid', 'asc')->get();
        // 從資料庫取得可設定的權限
        $info['priv'] = UserPriviledge::orderBy('privNum', 'asc')->get();
        // 處理一個陣列用於顯示會員權限
        // 格式為 $userpriv[權限ID] = 權限名稱
        $userpriv = [];
        foreach($info['priv'] as $priv){
            $userpriv[$priv->privNum] = $priv->privName;
        }
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '會員帳號新增與一覽'],
        ];
        return view('backend.member.user.userform', compact('bc', 'info', 'userpriv'));
    }

    /**
     * 管理會員資料表單
     * @param Request $request Request 實例
     * @param int $uid 使用者 ID
     * @return redirect 重新導向實例
     */
    public function editUser(Request $request, $uid)
    {
        $uBuilder = User::where('uid', $uid);
        // 如果找不到該帳號
        if($uBuilder->count() == 0){
            return redirect(route('admin.member.user', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該帳號！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建管理員
        elseif($uid == 1){
            return redirect(route('admin.member.user', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的管理員不可編輯！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $userdata = $uBuilder->first();
        $privs = UserPriviledge::orderBy('privNum', 'asc')->get();
        $bc = [
            ['url' => route('admin.member.user', ['action'=> 'list']), 'name' => '會員帳號新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['uid'=> $uid]), 'name' => '管理使用者「' . $userdata->userName . '」'],
        ];
        return view('backend.member.user.edituser', compact('bc', 'userdata', 'privs'));
    }

    /**
     * 刪除確認表單
     * @param Request $request Request 實例
     * @param int $uid 使用者 ID
     * @return view 視圖
     */
    public function delUserConfirm(Request $request, $uid)
    {
        $uBuilder = User::where('uid', $uid);
        // 如果找不到該帳號
        if($uBuilder->count() == 0){
            return redirect(route('admin.member.user', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該帳號！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建管理員
        elseif($uid == 1){
            return redirect(route('admin.member.user', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的管理員不可刪除！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $userdata = $uBuilder->first();
        $priv = User::find($uid)->priv()->where('privNum', $userdata->userPriviledge)->value('privName');
        $bc = [
            ['url' => route('admin.member.user', ['action'=> 'list']), 'name' => '會員帳號新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['uid'=> $uid]), 'name' => '確認刪除使用者帳號「' . $userdata->userName . '」'],
        ];
        return view('backend.member.user.deluserconfirm', compact('bc', 'userdata', 'priv'));
    }

    /**
     * 搜尋會員結果
     * @param $resultdata 搜尋結果資料
     * @param $inputdata 輸入的搜尋表單資料
     * @return view 視圖
     */
    public function searchResult($result, $inputdata)
    {
        $bc = [
            ['url' => route('admin.member.user', ['action'=> 'search']), 'name' => '會員帳號新增與一覽'],
            ['url' => route(Route::currentRouteName()), 'name' => '會員搜尋結果'],
        ];
        return view('backend.member.user.searchresult', compact('bc', 'result', 'inputdata'));
    }

    /**
     * 執行搜尋會員
     * @param Request $request Request 實例
     * @return redirect 重新導向實例
     */
    public function fireSearchUser(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'searchuser' => ['required', 'string', 'max:50'],
            'searchtarget' => ['required', 'string', Rule::in(['uid', 'username', 'usernickname'])],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return redirect(route('admin.member.user', ['action'=> 'search']))
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就判斷蒐尋目標下去取資料
        switch($request->input('searchtarget')){
            // 依 UID 搜尋
            case 'uid':
                $resultdata = [
                    'data' => User::where('uid', $request->input('searchuser'))->get(),
                    'nums' => User::where('uid', $request->input('searchuser'))->count(),
                ];
                break;
            // 依使用者名稱搜尋
            case 'username':
                $resultdata = [
                    'data'=> User::where('username', 'like', '%' . $request->input('searchuser') . '%')->get(),
                    'nums'=> User::where('username', 'like', '%' . $request->input('searchuser') . '%')->count(),
                ];
                break;
            // 依使用者暱稱搜尋
            case 'usernickname':
                $resultdata = [
                    'data'=> User::where('usernickname', 'like', '%' . $request->input('searchuser') . '%')->get(),
                    'nums'=> User::where('usernickname', 'like', '%' . $request->input('searchuser') . '%')->count(),
                ];
                break;
            default:
                return redirect(route('admin.member.user', ['action'=> 'search']))
                       ->withErrors([
                           'msg' => '請依正常程序搜尋會員',
                           'type' => 'error',
                       ])
                       ->withInput();
        }
        $inputdata = [
            'text'=> $request->input('searchuser'),
            'type'=> $request->input('searchtarget'),
        ];
        // 回呼搜尋結果方法
        return $this->searchResult($resultdata, $inputdata);
    }

    /**
     * 執行新增會員
     * @param Request $request Request 實例
     * @return redirect 重新導向實例
     */
    public function addUser(Request $request)
    {
        // 先取得有哪些權限
        $privBuilder = UserPriviledge::orderBy('privNum', 'asc')->get(['privNum']);
        $privs = [];
        foreach($privBuilder as $priv){
            array_push($privs, $priv->privNum);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50', 'unique:member,userName'],
            'userpriviledge' => ['required', 'int', Rule::in($privs)],
            'usernickname' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            'email' => ['required', 'string', 'email', 'max:50'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就寫入資料庫
        User::create([
            'userName' => $request->input('username'),
            'userNickname' => $request->input('usernickname'),
            'userPW' => Hash::make($request->input('username')),
            'userEmail' => $request->input('email'),
            'userPriviledge' => $request->input('userpriviledge'),
        ]);
        return redirect(route('admin.member.user', ['action' => 'list']))->withErrors([
            'msg'=> '成功註冊新帳號！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯會員資料
     * @param Request $request Request 實例
     * @param int $uid 會員編號
     * @return redirect 重新導向實例
     */
    public function fireEditUser(Request $request, $uid)
    {
        $uBuilder = User::where('uid', $uid);
        // 如果找不到該帳號
        if($uBuilder->count() == 0){
            return back()
                   ->withInput()
                   ->withErrors([
                'msg'=> '找不到該帳號！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建管理員
        elseif($uid == 1){
            return back()
                   ->withInput()
                   ->withErrors([
                'msg'=> '內建的管理員不可編輯！',
                'type'=> 'error',
            ]);
        }
        // 再檢查是不是有上傳檔案又把刪除檔案打勾
        elseif($request->hasFile('avatorimage') && $request->has('delavatorimage')){
            return back()
                   ->withInput()
                   ->withErrors([
                       'msg'=> '上傳與刪除虛擬形象不能同時執行！',
                       'type'=> 'error',
                   ]);
        }
        // 先取得有哪些權限
        $privBuilder = UserPriviledge::orderBy('privNum', 'asc')->get(['privNum']);
        $privs = [];
        foreach($privBuilder as $priv){
            array_push($privs, $priv->privNum);
        }
        // 再判斷修不修改密碼
        switch(empty($request->input('password'))){
            // 要修改
            case false:
                // 驗證輸入的表單內容
                $validator = Validator::make($request->all(), [
                    'userpriviledge' => ['required', 'int', Rule::in($privs)],
                    'usernickname' => ['required', 'string', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:50'],
                    'userrealname' => ['nullable', 'string', 'max:50'],
                    'userphone' => ['nullable', 'regex:/(0)[0-9]{9}/'],
                    'useraddress' => ['nullable', 'string', 'max:100'],
                    'password' => ['required', 'string', 'min:3', 'confirmed'],
                    'avatorimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
                    'delavatorimage' => ['nullable', Rule::in(['true'])],
                ]);
                break;
            // 不修改
            default:
                // 驗證輸入的表單內容
                $validator = Validator::make($request->all(), [
                    'userpriviledge' => ['required', 'int', Rule::in($privs)],
                    'usernickname' => ['required', 'string', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:50'],
                    'userrealname' => ['nullable', 'string', 'max:50'],
                    'userphone' => ['nullable', 'regex:/(0)[0-9]{9}/'],
                    'useraddress' => ['nullable', 'string', 'max:100'],
                    'avatorimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
                    'delavatorimage' => ['nullable', Rule::in(['true'])],
                ]);
        }
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒有錯誤就開始更新資料與檔案
        $userdata = $uBuilder->first();
        // 有上傳檔案就先處理檔案
        if($request->hasFile('avatorimage')){
            // 如果本來就不是預設的虛擬形象則需要先把檔案刪除
            if($userdata->userAvator != 'exampleAvator.jpg'){
                $oldFilename = 'images/userAvator/'. $userdata->userAvator;
                Storage::disk('htdocs')->delete($oldFilename);
            }
            // 決定新檔案名稱和副檔名
            $filename = 'user-' . hexdec(uniqid()) . '.' . $request->file('avatorimage')->extension();
            // 移動檔案
            $request->file('avatorimage')->storeAs('images/userAvator/', $filename, 'htdocs');
        }
        // 如果沒傳檔案但是要刪除檔案
        elseif($request->has('delavatorimage') && $request->input('delavatorimage') == 'true'){
            $oldFilename = 'images/userAvator/'. $userdata->userAvator;
            Storage::disk('htdocs')->delete($oldFilename);
            $filename = 'exampleAvator.jpg';
        }
        // 不做任何動作
        else{
            // 沒有傳檔案還是要從 Auth 裡取出檔案名稱，方便資料庫更新
            $filename = $userdata->userAvator;
        }
        // 然後更新資料庫資料
        switch(empty($request->input('password'))){
            // 要更新密碼
            case false:
                $uBuilder->update([
                    'userPW' => Hash::make($request->input('password')),
                    'userNickname' => $request->input('usernickname'),
                    'userAvator' => $filename,
                    'userEmail' => $request->input('email'),
                    'userPriviledge' => $request->input('userpriviledge'),
                    'userRealName' => $request->input('userrealname'),
                    'userPhone' => $request->input('userphone'),
                    'userAddress' => $request->input('useraddress'),
                ]);
                break;
            // 不更新密碼
            default:
                $uBuilder->update([
                    'userNickname' => $request->input('usernickname'),
                    'userAvator' => $filename,
                    'userEmail' => $request->input('email'),
                    'userPriviledge' => $request->input('userpriviledge'),
                    'userRealName' => $request->input('userrealname'),
                    'userPhone' => $request->input('userphone'),
                    'userAddress' => $request->input('useraddress'),
                ]);
        }
        return redirect(route('admin.member.user', ['action'=> 'list']))
        ->withErrors([
            'msg'=> '更新會員資料成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除會員資料
     * @param Request $request Request 實例
     * @param int $uid 會員編號
     * @return redirect 重新導向實例
     */
    public function fireDelUser(Request $request, $uid)
    {
        $uBuilder = User::where('uid', $uid);
        // 如果找不到該帳號
        if($uBuilder->count() == 0){
            return back()
                   ->withErrors([
                'msg'=> '找不到該帳號！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建管理員
        elseif($uid == 1){
            return back()
                   ->withErrors([
                'msg'=> '內建的管理員不可刪除！',
                'type'=> 'error',
            ]);
        }
        // 沒有問題就開始準備刪除資料
        $userdata = $uBuilder->first();
        // 只要是這個帳號貼的主貼文都完全刪除
        $posts = User::find($uid)->posts()->get();
        // 找出哪些回文要被刪除
        $targetReplies = [];
        foreach($posts as $post){
            array_push($targetReplies, $post);
        }
        // 刪除上面找到的回文 ID 和由這支帳號發出的回文
        BBSArticle::whereIn('articlePost', $targetReplies)
                    ->orWhere('articleUserID', $userdata->userName)
                    ->delete();
        // 刪除由這支帳號張貼的文章
        User::find($uid)->posts()->delete();
        // 刪除與這支帳號有關的通知
        User::find($uid)->notifications()->delete();
        // 刪除與這支帳號有關的訂單
        $orders = User::find($uid)->orders()->get();
        // 找出哪些訂單詳細項目要被刪除
        $targetDetail = [];
        foreach($orders as $order){
            array_push($targetDetail, $order->orderID);
        }
        // 刪除詳細項目
        OrderDetail::whereIn('orderID', $targetDetail)->delete();
        // 刪除曾申請過的取消訂單項目
        RemoveOrders::whereIn('targetOrder', $targetDetail)->delete();
        // 刪除訂單
        $orders = User::find($uid)->orders()->delete();
        // 刪除所有登入階段
        User::find($uid)->sessions()->delete();
        // 刪除帳號資料
        $uBuilder->delete();
        // 刪除虛擬形象檔案
        if($userdata->userAvator != 'exampleAvator.jpg'){
            $oldFilename = 'images/userAvator/'. $userdata->userAvator;
            Storage::disk('htdocs')->delete($oldFilename);
        }
        // 重新導向回管理一覽頁面
        return redirect(route('admin.member.user', ['action'=> 'list']))->withErrors([
            'msg'=> '成功刪除帳號及與之相關的貼文、回文、通知、訂單和登入階段資料！',
            'type'=> 'success',
        ]);
    }
}
