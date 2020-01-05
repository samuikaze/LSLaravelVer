<?php

namespace App\Http\Controllers\Backend\Goods;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\Goods;
use App\Models\Notifications;
use App\Models\Orders;
use App\Models\User;

class OrderController extends Controller
{
    /**
     * 訂單一覽
     * @param Request $request Request 實例
     * @param string $action 目前顯示頁面
     * @return view 視圖
     */
    public function orderindex(Request $request, $action)
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
        // 總頁數和資料一起處理起來
        // 進行中訂單
        $info['inprogress'] = [
            'nums' => Orders::whereNotIn('orderStatus', ['已申請取消訂單', '訂單已取消', '已取貨', '已結單'])->count(),
            'data' => Orders::whereNotIn('orderStatus', ['已申請取消訂單', '訂單已取消', '已取貨', '已結單'])->skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('orderID', 'asc')->get(),
        ];
        $info['inprogress']['totalPage'] = ceil($info['inprogress']['nums'] / $info['listnums']);
        // 退訂訂單
        $info['refund'] = [
            'nums' => Orders::whereIn('orderStatus', ['已申請取消訂單', '訂單已取消'])->count(),
            'data' => Orders::whereIn('orderStatus', ['已申請取消訂單', '訂單已取消'])->skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('orderID', 'asc')->get(),
        ];
        $info['refund']['totalPage'] = ceil($info['refund']['nums'] / $info['listnums']);
        // 已結單訂單
        $info['finish'] = [
            'nums' => Orders::whereIn('orderStatus', ['已取貨', '已結單'])->count(),
            'data' => Orders::whereIn('orderStatus', ['已取貨', '已結單'])->skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('orderID', 'asc')->get(),
        ];
        $info['finish']['totalPage'] = ceil($info['finish']['nums'] / $info['listnums']);
        // 麵包屑
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '訂單一覽'],
        ];
        return view('backend.goods.orders.orderform', compact('bc', 'info'));
    }

    /**
     * 檢視訂單詳細資料
     * @param Request $request Request 實例
     * @param int $oid 訂單 ID
     * @return view 視圖
     */
    public function orderDetail(Request $request, $oid)
    {
        $oBuilder = Orders::where('orderID', $oid);
        // 如果找不到該帳號
        if($oBuilder->count() == 0){
            return redirect(route('admin.goods.orders', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該訂單！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $data = [
            'order' => $oBuilder->first(),
            'detail' => Orders::find($oid)->orderdetail()->orderBy('goodID', 'asc')->get(),
        ];
        $data['user'] = User::where('userName', $data['order']->orderMember)->first();
        // 先取出訂單詳細資料中的商品 ID
        $gids = [];
        foreach($data['detail'] as $good){
            array_push($gids, $good->goodID);
        }
        // 然後去取商品資料
        $data['goods'] = Goods::whereIn('goodsOrder', $gids)->orderBy('goodsOrder', 'asc')->get();
        // 有申請取消訂單則還要去取該筆資料
        if($data['order']->orderStatus == '已申請取消訂單'){
            $data['refund'] = Orders::find($oid)->refund()->first();
            $data['refunddate'] = date('Y-m-d', strtotime($data['refund']->removeDate));
        }
        $bc = [
            ['url' => route('admin.goods.orders', ['action'=> 'list']), 'name' => '訂單一覽'],
            ['url' => route(Route::currentRouteName(), ['uid'=> $oid]), 'name' => '訂單' . $data['order']->orderID . '詳細資料'],
        ];
        return view('backend.goods.orders.orderdetail', compact('bc', 'data'));
    }

    /**
     * 執行通知已出貨或已結單
     * @param Request $request Request 實例
     * @param int $oid 訂單 ID
     * @return Redirect 重新導向實例
     */
    public function modifyOrderStatus(Request $request, $oid)
    {
        $oBuilder = Orders::where('orderID', $oid);
        // 如果找不到該帳號
        if($oBuilder->count() == 0){
            return redirect(route('admin.goods.orders', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該訂單！',
                'type'=> 'error',
            ]);
        }
        $order = $oBuilder->first();
        // 驗證表單
        $validator = Validator::make($request->all(), [
            'action'=> ['required', 'string', Rule::in('finishorder', 'notifyshipped')],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒錯就依 action 欄位判斷要如何更新資料庫
        switch($request->input('action')){
            // 通知已出貨
            case 'notifyshipped':
                $oBuilder->update([
                    'orderStatus'=> '已出貨',
                ]);
                // 更新商品數量
                // 取訂單內的商品資料
                $goods = Orders::find($oid)->orderdetail()->get();
                // 處理 SQL 語句
                $orderCondition = "";
                $inCondition = "";
                foreach($goods as $good){
                    if(empty($inCondition)){
                        $inCondition = $good->goodID;
                    }else{
                        $inCondition .= "," . $good->goodID;
                    }
                    $orderCondition .= " WHEN " . $good->goodID . " THEN `goodsQty`-" . $good->goodQty;
                }
                $sql = "UPDATE `goodslist` SET `goodsQty`=CASE `goodsOrder` $orderCondition END WHERE `goodsOrder` IN ($inCondition)";
                // 更新資料庫
                DB::statement(
                    DB::raw($sql)
                );
                // 給予通知
                Notifications::create([
                    'notifyContent'=> '訂單編號' . $order->orderSerial . '內的商品已經為您出貨，待物流送達後即可取貨！',
                    'notifyTitle'=> '您的商品已出貨！',
                    'notifySource'=> '洛嬉遊戲訂單管理組',
                    'notifyTarget'=> $order->orderMember,
                    'notifyURL'=> route('dashboard.orderdetail', ['serial'=> $order->orderSerial]),
                ]);
                $msg = '通知已出貨';
                break;
            // 結單
            case 'finishorder':
                $oBuilder->update([
                    'orderStatus'=> '已結單',
                ]);
                $msg = '變更訂單狀態';
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序修改訂單狀態',
                           'type'=> 'error',
                        ])
                       ->withInput();
        }
        return Redirect(route('admin.goods.orderdetail', ['oid'=> $oid]))->withErrors([
            'msg'=> $msg . '成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 審核退訂申請
     * @param Request $request Request 實例
     * @param int $oid 訂單 ID
     * @return Redirect 重新導向實例
     */
    public function reviewRefund(Request $request, $oid)
    {
        $oBuilder = Orders::where('orderID', $oid);
        // 如果找不到該帳號
        if($oBuilder->count() == 0){
            return redirect(route('admin.goods.orders', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該訂單！',
                'type'=> 'error',
            ]);
        }
        // 如果找不到退訂申請
        elseif(Orders::find($oid)->refund()->count() == 0){
            return redirect(route('admin.goods.orders', ['action'=> 'list']))->withErrors([
                'msg'=> '該訂單並沒有申請取消！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'reviewResult'=> ['required', 'string', Rule::in('true', 'false')],
            'reviewNotify'=> ['required', 'string', 'max:150'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        $order = $oBuilder->first();
        // 沒錯就依照審核結果更新資料庫
        switch($request->input('reviewResult')){
            // 通過申請
            case 'true':
                // 更新訂單資料
                $oBuilder->update([
                    'orderStatus'=> '訂單已取消',
                ]);
                // 更新申請記錄
                Orders::find($oid)->refund()->update([
                    'removeStatus'=> 'passed',
                ]);
                // 給予通知
                Notifications::create([
                    'notifyContent'=> '訂單編號' . $order->orderSerial . '的取消申請已經通過！',
                    'notifyTitle'=> '您的訂單已取消！',
                    'notifySource'=> '洛嬉遊戲訂單管理組',
                    'notifyTarget'=> $order->orderMember,
                    'notifyURL'=> route('dashboard.orderdetail', ['serial'=> $order->orderSerial]),
                ]);
                break;
            // 駁回申請
            case 'false':
                // 更新訂單資料
                $oBuilder->update([
                    'orderStatus'=> $order->orderApplyStatus,
                    'removeApplied'=> 0,
                    'orderApplyStatus'=> null,
                ]);
                // 移除申請記錄
                Orders::find($oid)->refund()->delete();
                // 給予通知
                Notifications::create([
                    'notifyContent'=> '訂單編號' . $order->orderSerial . '的取消申請被駁回，原因為「' . $request->input('reviewNotify') . '」，訂單目前的狀態為退訂前的狀態，您可以依要求重新提出一次取消申請！',
                    'notifyTitle'=> '您的訂單已取消！',
                    'notifySource'=> '洛嬉遊戲訂單管理組',
                    'notifyTarget'=> $order->orderMember,
                    'notifyURL'=> route('dashboard.orderdetail', ['serial'=> $order->orderSerial]),
                ]);
                break;
            default:
                return back()
                       ->withErrors([
                           'msg' => '請依正常程序審核退訂申請！',
                           'type'=> 'error',
                       ])
                       ->withInput();
        }
        return redirect(route('admin.goods.orderdetail', ['oid'=> $oid]))->withErrors([
            'msg'=> '審核退訂申請成功！',
            'type'=> 'success',
        ]);
    }
}
