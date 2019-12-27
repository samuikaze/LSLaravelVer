<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Goods;
use App\Models\Orders;
use App\Models\RemoveOrders;
use Cookie;

class DashboardController extends Controller
{
    /**
     * 顯示會員儀錶板表單資料
     * @param Request $request Request 實例
     * @return view 視圖
     */
    public function showData(Request $request)
    {
        /**
         * 從 URL 取得一開始要顯示的頁面是哪個
         * a 可以有以下三種參數，為空時預設選擇第一項
         * 分別為「資料管理」、「訂單管理」和「登入管理」
         * userdata、userorders、usersessions
         */
        if(!empty($request->query('a'))){
            if(in_array($request->query('a'), ['userdata', 'userorders', 'usersessions'])){
                $board['display'] = $request->query('a');
            }else{
                return redirect(route('dashboard.form'))
                       ->withErrors([
                           'msg'=> '沒有該功能，重新導向至使用者設定頁面',
                           'type'=> 'error',
                       ]);
            }
        }else{
            $board['display'] = 'userdata';
        }
        // 取得使用者的資料後再修改權限的名稱
        $userdata = User::where('uid', Auth::user()->uid)->first();
        $userdata->userPriviledge = User::find(Auth::user()->uid)->priv()->value('privName');
        // 訂單資料
        $orderBulider = User::find(Auth::user()->uid)->orders();
        $orderdata = [
            'nums'=> $orderBulider->count(),
            'data'=> $orderBulider->get(),
        ];
        // 登入階段資料
        $loginSessionBuilder = User::find(Auth::user()->uid)->sessions();
        $sessiondata = [
            'nums'=> $loginSessionBuilder->count(),
            'data'=> $loginSessionBuilder->get(),
        ];
        // 找出目前的登入階段在哪個索引
        foreach($sessiondata['data'] as $i => $sdd){
            if($sdd->sessionID == Cookie::get('loginSession')){
                $sessiondata['thisIndex'] = $i;
                break;
            }
        }
        $bc = [
            ['url' => route('dashboard.form', ['?a=' . $board['display']]), 'name' => '帳號管理'],
        ];
        return view('frontend.dashboard.mainform', compact('bc', 'board', 'userdata', 'orderdata', 'sessiondata'));
    }

    /**
     * 顯示訂單詳細資料
     * @param Request $request Request 實例
     * @param int $serial 訂單序號
     * @return view 視圖
     */
    public function orderDetail(Request $request, $serial)
    {
        $orderBuilder = User::find(Auth::user()->uid)->orders()->where('orderSerial', $serial);
        // 找不到訂單編號就踢回上一頁
        if($orderBuilder->count() == 0){
            return back()->withErrors([
                'msg'=> '找不到該訂單編號，請依正常程序檢視訂單詳細資料！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就開始處理資料
        // 訂單基本資料
        $rawdata = $orderBuilder->first();
        // 訂單商品資料
        $rawdetail = Orders::find($rawdata->orderID)->orderdetail()->orderBy('goodID', 'ASC')->get();
        $orderdata = [
            'serial'=> $serial,
            'realname'=> $rawdata->orderRealName,
            'phone'=> $rawdata->orderPhone,
            'address'=> $rawdata->orderAddress,
            'total'=> $rawdata->orderPrice,
            'date'=> $rawdata->orderDate,
            'casher'=> (empty($rawdata->orderCasher)) ? '取貨付款' : $rawdata->orderCasher,
            'pattern'=> $rawdata->orderPattern,
            'freight'=> $rawdata->orderFreight,
            'status'=> $rawdata->orderStatus,
        ];
        // 處理商品資料
        $goodids = [];
        // 處理商品編號 ID 陣列
        foreach($rawdetail as $good){
            array_push($goodids, $good->goodID);
        }
        // 取相關商品資料
        $rawgooddata = Goods::whereIn('goodsOrder', $goodids)->orderBy('goodsOrder', 'ASC')->get();
        $detaildata = [];
        // 處理成要返回給 view 的格式
        foreach($rawgooddata as $i=> $rd){
            array_push($detaildata, [
                'image'=> $rd->goodsImgUrl,
                'name'=> $rd->goodsName,
                // 由於都有依照商品編號排序，所以現在循環到的商品資料索引值和訂單商品索引值會互相對應
                'price'=> $rawdetail[$i]->goodPrice,
                'qty'=> $rawdetail[$i]->goodQty,
                'total'=> $rawdetail[$i]->goodPrice * $rawdetail[$i]->goodQty,
            ]);
        }
        $bc = [
            ['url' => route('dashboard.form', ['a'=> 'userorders']), 'name' => '帳號管理'],
            ['url' => route(Route::currentRouteName(), ['serial'=> $serial]), 'name' => '訂單詳細資料'],
        ];
        return view('frontend.dashboard.orderdetail', compact('bc', 'orderdata', 'detaildata'));
    }

    /**
     * 申請退訂表單
     * @param Request $request Request 實例
     * @param int $serial 訂單序號
     * @return view 視圖
     */
    public function removeOrder(Request $request, $serial)
    {
        $orderBuilder = User::find(Auth::user()->uid)->orders()->where('orderSerial', $serial);
        // 沒有這筆訂單或已經申請過退訂或早就被取消的訂單就踢回上一頁
        if($orderBuilder->count() == 0 || $orderBuilder->value('removeApplied') == 1 || $orderBuilder->value('orderStatus') == '訂單已取消'){
            return back()->withErrors([
                'msg'=> '找不到該訂單編號、已經申請過退訂或訂單已經被取消，請依正常程序申請退訂！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就開始處理資料
        // 訂單基本資料
        $rawdata = $orderBuilder->first();
        // 訂單商品資料
        $rawdetail = Orders::find($rawdata->orderID)->orderdetail()->orderBy('goodID', 'ASC')->get();
        $orderdata = [
            'serial'=> $serial,
            'realname'=> $rawdata->orderRealName,
            'phone'=> $rawdata->orderPhone,
            'address'=> $rawdata->orderAddress,
            'total'=> $rawdata->orderPrice,
            'date'=> $rawdata->orderDate,
            'casher'=> (empty($rawdata->orderCasher)) ? '取貨付款' : $rawdata->orderCasher,
            'pattern'=> $rawdata->orderPattern,
            'freight'=> $rawdata->orderFreight,
            'status'=> $rawdata->orderStatus,
        ];
        // 處理商品資料
        $goodids = [];
        // 處理商品編號 ID 陣列
        foreach($rawdetail as $good){
            array_push($goodids, $good->goodID);
        }
        // 取相關商品資料
        $rawgooddata = Goods::whereIn('goodsOrder', $goodids)->orderBy('goodsOrder', 'ASC')->get();
        $detaildata = [];
        // 處理成要返回給 view 的格式
        foreach($rawgooddata as $i=> $rd){
            array_push($detaildata, [
                'image'=> $rd->goodsImgUrl,
                'name'=> $rd->goodsName,
                // 由於都有依照商品編號排序，所以現在循環到的商品資料索引值和訂單商品索引值會互相對應
                'price'=> $rawdetail[$i]->goodPrice,
                'qty'=> $rawdetail[$i]->goodQty,
                'total'=> $rawdetail[$i]->goodPrice * $rawdetail[$i]->goodQty,
            ]);
        }
        $bc = [
            ['url' => route('dashboard.form', ['a'=> 'userorders']), 'name' => '帳號管理'],
            ['url' => route(Route::currentRouteName(), ['serial'=> $serial]), 'name' => '申請取消訂單'],
        ];
        return view('frontend.dashboard.removeorder', compact('bc', 'orderdata', 'detaildata'));
    }

    /**
     * 執行申請取消訂單
     * @param Request $request Request 實例
     * @param int $serial 訂單序號
     * @return Redirect 實例
     */
    public function doRemoveOrder(Request $request, $serial)
    {
        $orderBuilder = User::find(Auth::user()->uid)->orders()->where('orderSerial', $serial);
        // 沒有這筆訂單或已經申請過退訂或早就被取消的訂單就踢回上一頁
        if($orderBuilder->count() == 0 || $orderBuilder->value('removeApplied') == 1 || $orderBuilder->value('orderStatus') == '訂單已取消'){
            return back()->withErrors([
                'msg'=> '找不到該訂單編號、已經申請過退訂或訂單已經被取消，請依正常程序申請退訂！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(),[
            'removereason' => ['required', 'string', 'min:30', 'max:100'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就取資料更新資料庫
        $orderData = $orderBuilder->first();
        RemoveOrders::create([
            'targetOrder'=> $orderData->orderID,
            'removeReason'=> $request->input('removereason'),
        ]);
        // 然後更新訂單狀態
        $orderBuilder->update([
            'orderStatus'=> '已申請取消訂單',
            'removeApplied'=> 1,
            'orderApplyStatus'=> $orderData->orderStatus,
        ]);
        return redirect(route('dashboard.form', ['a'=> 'userorders']))->withErrors([
            'msg'=> '申請取消訂單成功，請靜待團隊處理您的訂單',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行更新使用者資料
     * @param Request $request Request 實例
     * @return Redirect redirect 實例
     */
    public function updateUserData(Request $request)
    {
        // 先檢查是不是有上傳檔案又把刪除檔案打勾
        if($request->hasFile('avatorimage') && $request->has('delavatorimage')){
            return back()
                   ->withErrors([
                       'msg'=> '上傳與刪除虛擬形象不能同時執行！',
                       'type'=> 'error',
                   ]);
        }
        // 再判斷修不修改密碼
        switch(empty($request->input('password'))){
            // 要修改
            case false:
                // 驗證輸入的表單內容
                $validator = Validator::make($request->all(),[
                    'avatorimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
                    'delavatorimage' => ['nullable', Rule::in(['true'])],
                    'password' => ['required', 'string', 'min:3', 'confirmed'],
                    'usernickname' => ['required', 'string', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:50'],
                    'userrealname' => ['nullable', 'string', 'max:50'],
                    'userphone' => ['nullable', 'regex:/(0)[0-9]{9}/'],
                    'useraddress' => ['nullable', 'string', 'max:100'],
                ]);
                break;
            // 不修改
            default:
                // 驗證輸入的表單內容
                $validator = Validator::make($request->all(),[
                    'avatorimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
                    'delavatorimage' => ['nullable', Rule::in(['true'])],
                    'usernickname' => ['required', 'string', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:50'],
                    'userrealname' => ['nullable', 'string', 'max:50'],
                    'userphone' => ['nullable', 'regex:/(0)[0-9]{9}/'],
                    'useraddress' => ['nullable', 'string', 'max:100'],
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
        // 有上傳檔案就先處理檔案
        if($request->hasFile('avatorimage')){
            // 如果本來就不是預設的虛擬形象則需要先把檔案刪除
            if(Auth::user()->userAvator != 'exampleAvator.jpg'){
                $oldFilename = 'images/userAvator/'. Auth::user()->userAvator;
                Storage::disk('htdocs')->delete($oldFilename);
            }
            // 決定新檔案名稱和副檔名
            $filename = 'user-' . hexdec(uniqid()) . '.' . $request->file('avatorimage')->extension();
            // 移動檔案
            $request->file('avatorimage')->storeAs('images/userAvator/', $filename, 'htdocs');
        }
        // 如果沒傳檔案但是要刪除檔案
        elseif($request->has('delavatorimage') && $request->input('delavatorimage') == 'true'){
            $oldFilename = 'images/userAvator/'. Auth::user()->userAvator;
            Storage::disk('htdocs')->delete($oldFilename);
            $filename = 'exampleAvator.jpg';
        }
        // 不做任何動作
        else{
            // 沒有傳檔案還是要從 Auth 裡取出檔案名稱，方便資料庫更新
            $filename = Auth::user()->userAvator;
        }
        // 然後更新資料庫資料
        switch(empty($request->input('password'))){
            // 要更新密碼
            case false:
                User::where('uid', Auth::user()->uid)->update([
                    'userPW' => Hash::make($request->input('password')),
                    'userNickname' => $request->input('usernickname'),
                    'userAvator' => $filename,
                    'userEmail' => $request->input('email'),
                    'userRealName' => $request->input('userrealname'),
                    'userPhone' => $request->input('userphone'),
                    'userAddress' => $request->input('useraddress'),
                ]);
                // 更新完後請使用者重新登入驗證新密碼
                return redirect(route('dashboard.form'))
                       ->withErrors([
                           'msg'=> '更新使用者資料成功，由於密碼已更新，請重新登入驗證新密碼',
                           'type'=> 'success',
                       ]);
                break;
            // 不更新密碼
            default:
                User::where('uid', Auth::user()->uid)->update([
                    'userNickname' => $request->input('usernickname'),
                    'userAvator' => $filename,
                    'userEmail' => $request->input('email'),
                    'userRealName' => $request->input('userrealname'),
                    'userPhone' => $request->input('userphone'),
                    'userAddress' => $request->input('useraddress'),
                ]);
                // 更新完就直接回使用者設定頁面
                return redirect(route('dashboard.form'))
                       ->withErrors([
                           'msg'=> '更新使用者資料成功',
                           'type'=> 'success',
                       ]);
        }
    }

    /**
     * 執行登出工作階段
     * @param Request $request Request 實例
     * @param int $sid 登入階段的 ID
     * @return Redirect redirect 實例
     */
    public function logoutSession(Request $request, $sid)
    {
        // 先檢查要登出的階段是不是這位使用者的階段
        if(User::find(Auth::user()->uid)->sessions()->where('sID', $sid)->count() == 0){
            return redirect(route('dashboard.form', ['a=usersessions']))
                   ->withErrors([
                       'msg'=> '沒有該登入階段，請依正常程序登出工作階段',
                       'type'=> 'error',
                   ]);
        }
        // 沒有跳轉就開始清資料庫
        User::find(Auth::user()->uid)->sessions()->where('sID', $sid)->delete();
        return redirect(route('dashboard.form', ['a=usersessions']))
               ->withErrors([
                   'msg'=> '該登入階段已被登出！',
                   'type'=> 'success',
               ]);
    }

    /**
     * 檢視通知
     * @param Request $request Request 實例
     * @return view 視圖
     */
    public function viewnotify(Request $request)
    {
        $nBuilder = User::find(Auth::user()->uid)->notifications();
        // 取通知資料
        $notifyrawdata = $nBuilder->get();
        // 處理顯示資料
        $notifydata = [
            'totalNums'=> $nBuilder->count(),
            'unreadNums'=> $nBuilder->where('notifyStatus', 'u')->count(),
            'data'=> $notifyrawdata,
        ];
        $bc = [
            ['url' => route(Route::currentRouteName()), 'name' => '通知一覽'],
        ];
        return view('frontend.dashboard.notifications', compact('bc', 'notifydata'));
    }

    /**
     * [AJAX] 執行已讀單則或全部通知
     * @param Request $request Request 實例
     * @return JSON json 回應 
     */
    public function readNotify(Request $request)
    {
        // 如果 POST 過來的資料沒有 action 欄位就踢走
        if(empty($request->input('action'))){
            return response()->json(['error'=> '請依正常程序已讀通知！'], 400);
        }
        // 判斷輸入的值來決定是要已讀單則還是全部已讀
        switch($request->input('action')){
            // 已讀所有通知
            case 'readallnotify':
                // 如果沒有未讀訊息就返回錯誤訊息
                if(User::find(Auth::user()->uid)->notifications()->where('notifyStatus', 'u')->count() == 0){
                    return response()->json(['error'=> '沒有未讀通知可以已讀！'], 400);
                }
                // 更新資料庫
                User::find(Auth::user()->uid)->notifications()->where('notifyStatus', 'u')->update([
                    'notifyStatus'=> 'r',
                ]);
                // 返回回應
                return response()->json(['result'=> 'success', 'unreadnums'=> 0], 200);
                break;
            // 已讀單則通知
            default:
                // 如果沒有該則通知就返回錯誤訊息
                if(User::find(Auth::user()->uid)->notifications()->where('notifyID', $request->input('notifyid'))->count() == 0){
                    return response()->json(['error'=> '請依正常程序移除通知！'], 400);
                }
                // 先驗證資料
                $validator = Validator::make($request->all(), [
                    'notifyid' => ['required', 'int'],
                    'isgoto' => ['required', 'string', Rule::in(['true', 'false'])],
                ]);
                // 若驗證失敗
                if ($validator->fails()) {
                    return response()->json(['error'=> '請依正常程序已讀通知！'], 400);
                }
                // 沒問題就更新資料庫
                User::find(Auth::user()->uid)->notifications()->where('notifyID', $request->input('notifyid'))->update([
                    'notifyStatus'=> 'r',
                ]);
                // 取得未讀通知數量
                $unreadnums = User::find(Auth::user()->uid)->notifications()->where('notifyStatus', 'u')->count();
                if($request->input('isgoto') == 'true'){
                    $link = true;
                }else{
                    $link = false;
                }
                // 返回回應
                return response()->json(['result'=> 'success', 'unreadnums'=> $unreadnums, 'link'=> $link], 200);
        }
    }

    /**
     * [AJAX] 移除單則或全部通知
     * @param Request $request Request 實例
     * @return JSON json 回應 
     */
    public function deleteNotify(Request $request)
    {
        // 如果 POST 過來的資料沒有 action 欄位就踢走
        if(empty($request->input('action'))){
            return response()->json(['error'=> '請依正常程序已讀通知！'], 400);
        }
        // 判斷輸入的值來決定是要刪除單則還是全部刪除
        switch($request->input('action')){
            // 刪除所有通知
            case 'delallnotify':
                // 如果沒有通知就返回錯誤訊息
                if(User::find(Auth::user()->uid)->notifications()->where('notifyTarget', Auth::user()->userName)->count() == 0){
                    return response()->json(['error'=> '請依正常程序移除通知！'], 400);
                }
                // 更新資料庫
                User::find(Auth::user()->uid)->notifications()->where('notifyTarget', Auth::user()->userName)->delete();
                // 返回回應
                return response()->json(['result'=> 'success', 'unreadnums'=> 0], 200);
                break;
            // 刪除單則通知
            default:
                // 如果沒有該則通知就返回錯誤訊息
                if(User::find(Auth::user()->uid)->notifications()->where('notifyID', $request->input('notifyid'))->count() == 0){
                    return response()->json(['error'=> '請依正常程序移除通知！'], 400);
                }
                // 先驗證資料
                $validator = Validator::make($request->all(), [
                    'notifyid' => ['required', 'int'],
                ]);
                // 若驗證失敗
                if ($validator->fails()) {
                    return response()->json(['error'=> '請依正常程序已讀通知！'], 400);
                }
                // 沒問題就更新資料庫
                User::find(Auth::user()->uid)->notifications()->where('notifyID', $request->input('notifyid'))->delete();
                // 取得未讀通知數量
                $unreadnums = User::find(Auth::user()->uid)->notifications()->where('notifyStatus', 'u')->count();
                // 取得所有通知數量
                $notifynums = User::find(Auth::user()->uid)->notifications()->where('notifyTarget', Auth::user()->userName)->count();
                // 返回回應
                return response()->json(['result'=> 'success', 'unreadnums'=> $unreadnums, 'notifynums'=> $notifynums], 200);
        }
    }

    /**
     * [AJAX] 執行通知已付款
     * @param Request $request Request 實例
     * @return JSON json 回應
     * $request->orderid
     */
    public function notifyPaid(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'orderid'=> ['required'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            return response()->json(['error'=> '請依正常程序操作！'], 400);
        }
        // 檢查該筆訂單存不存在
        if(User::find(Auth::user()->uid)->orders()->where('orderSerial', $request->orderid)->count() == 0){
            return response()->json(['error'=> '請依正常程序操作！'], 400);
        }
        // 沒問題就更新資料庫
        User::find(Auth::user()->uid)->orders()->where('orderSerial', $request->orderid)->update([
            'orderStatus'=> '已取貨',
        ]);
        return response()->json(['msg'=> '成功通知團隊您已付款，請稍待團隊為您出貨！'], 200);
    }

    /**
     * [AJAX] 執行通知已取貨
     * @param Request $request Request 實例
     * @return JSON json 回應
     */
    public function notifyTaked(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'orderid'=> ['required'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            return response()->json(['error'=> '請依正常程序操作！'], 400);
        }
        // 檢查該筆訂單存不存在
        if(User::find(Auth::user()->uid)->orders()->where('orderSerial', $request->orderid)->count() == 0){
            return response()->json(['error'=> '請依正常程序操作！'], 400);
        }
        // 沒問題就更新資料庫
        User::find(Auth::user()->uid)->orders()->where('orderSerial', $request->orderid)->update([
            'orderStatus'=> '已取貨',
        ]);
        return response()->json(['msg'=> '感謝您的訂購，歡迎再次使用本服務！', 'rurl'=> route('dashboard.removeorder', ['serial'=> $request->orderid])], 200);
    }
}
