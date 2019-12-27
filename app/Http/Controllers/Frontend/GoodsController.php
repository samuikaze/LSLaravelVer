<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CheckoutPattern;
use App\Models\Goods;
use App\Models\GlobalSettings;
use App\Models\User;
use App\Models\Orders;
use App\Models\OrderDetail;

class GoodsController extends Controller
{
    /**
     * 檢視商品一覽
     * @param Request $request Request 實例
     * @param int $page 頁碼
     * @return view 視圖
     */
    public function viewgoods(Request $request)
    {
        // 從網址取得目前的頁數
        if(!empty($request->query('p'))){
            $page['this'] = $request->query('p');
        }else{
            $page['this'] = 1;
        }
        // 目前頁數
        $cPage = $page['this'];
        // 設定每頁顯示幾行
        $goodsNum = GlobalSettings::where('settingName', 'goodsNum')->value('settingValue');
        // 計算頁數
        $page['total'] = ceil(Goods::count() / $goodsNum);
        // 取得特定頁面商品
        // 先以頁數算起始值
        $start = $goodsNum * ($page['this'] - 1);
        /**
         * 取資料
         * skip 是 SQL 中 LIMIT a, b 中的 a 值
         * take 是 SQL 中 LIMIT a, b 中的 b 值
         */
        $goods = Goods::skip($start)->take($goodsNum)->get();
        // 沒登入或是沒有購物車 session 就顯示 0
        if(!Auth::check() || empty($request->session()->get('cart')['total'])){
            $cartTotal = 0;
        }
        // 否則顯示 session 內的值
        else{
            $cartTotal = $request->session()->get('cart')['total'];
        }
        $ecpay = (empty($request->session()->get('cart')['ecpaycheckout'])) ? false : true;
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽']
        ];
        return view('frontend.goods.goods', compact('bc', 'goods', 'cartTotal', 'ecpay', 'page'));
    }

    /**
     * 顯示商品詳細資料
     * @param int $goodId 商品編號
     * @return view 視圖
     */
    public function goodsdetail(Request $request, $goodId)
    {
        // 取得商品數量顏色區分的閥值
        $goodQtyDanger = GlobalSettings::where('settingName', 'goodQtyDanger')->value('settingValue');
        // 取得目標商品的資料列
        $goodData = Goods::where('goodsOrder', $goodId)->first();
        $ecpay = (empty($request->session()->get('cart')['ecpaycheckout'])) ? false : true;
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽'],
            ['url' => route(Route::currentRouteName(), ['id' => $goodId]), 'name' => $goodData->goodsName]
        ];
        return view('frontend.goods.gooddetail', compact('goodData', 'goodQtyDanger', 'ecpay', 'bc'));
    }

    /**
     * 檢視購物車
     * @param Request $request Request 實例
     * @return view 視圖
     */
    public function viewcart(Request $request)
    {
        // 購物車不為空
        if(! empty($request->session()->get('cart')['goods'])){
            /**
             * 從 session 取出來的購物車變數
             * @param array $cart['goods'] [$goodID=>$qty] 購物車內商品 ID 的陣列
             * @param int $cart['total'] 購物車內商品的總金額
             * @var array $cart 購物車內容
             */
            $cart = $request->session()->get('cart');
            $cartinfo = [
                'nums'=> count($cart['goods']),
                'goods'=> Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(),
                'qty'=> $cart['goods'],
                'total'=> $cart['total'],
                'step'=> (empty($cart['checkoutstep'])) ? null : $cart['checkoutstep'],
                'ecpay'=> (empty($cart['ecpaycheckout'])) ? false : true,
            ];
        }
        // 購物車為空
        else{
            $cartinfo = [
                'nums'=> 0,
                'goods'=> null,
                'qty'=> null,
                'total'=> 0,
                'step'=> null,
                'ecpay'=> false,
            ];
        }
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽'],
            ['url' => route(Route::currentRouteName()), 'name' => '檢視購物車'],
        ];
        return view('frontend.goods.viewcart', compact('bc', 'cartinfo'));
    }

    /**
     * 結帳
     * @param Request $request Request 實例
     * @param int $step 目前步驟
     * @return view 視圖
     */
    public function checkout(Request $request, $step)
    {
        // 先檢查有沒有被禁止下訂
        if(Auth::user()->userPriviledge == 2){
            return redirect(route('goods.viewcart'))->withErrors([
                'msg'=> '由於您的購物行為不佳，團隊禁止您下訂任何訂單',
                'type'=> 'error',
            ]);
        }
        // 若購物車是空的
        if(empty($request->session()->get('cart')['goods'])){
            return back()->withErrors([
                'msg'=> '您的購物車內沒有商品或結帳階段過期，請將商品加入購物車後再試一次！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就判斷 $step 來決定要顯示的資料
        $cart = $request->session()->get('cart');
        // 先處理麵包屑
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽'],
            ['url' => route('goods.viewcart'), 'name' => '檢視購物車'],
            ['url' => route('goods.checkout', ['step'=> $step]), 'name' => '結帳'],
        ];
        switch($step){
            // 第一步 - 選擇結帳方式
            case 1:
                $checkoutinfo = [
                    'step'=> $step,
                    'title'=> '第一步 - 選擇結帳方式',
                    // 取得結帳方式
                    'pattern'=> CheckoutPattern::where('type', 'freight')->get(),
                    'selected'=> (empty($cart['coPattern'])) ? null : $cart['coPattern'],
                ];
                return view('frontend.goods.checkout.step1', compact('bc', 'checkoutinfo', 'cart'));
                break;
            // 第二步 - 填寫資料
            case 2:
                // 如果 $request 有 fPattern 這個 input 值就是從第一步過來的
                if($request->has('fPattern')){
                    // 先驗證資料
                    $validData = CheckoutPattern::where('type', 'freight')->get(['pattern']);
                    foreach($validData as $i=> $val){
                        if($i == 0){
                            $rules = $val->pattern;
                        }else{
                            $rules .= "," . $val->pattern;
                        }
                    }
                    // 驗證表單資料
                    $validator = Validator::make($request->all(), [
                        'fPattern' => ['required', 'string', 'in:'.$rules],
                    ]);
                    // 若驗證失敗
                    if ($validator->fails()) {
                        // 針對錯誤訊息新增一欄訊息類別
                        $validator->errors()->add('type', 'error');
                        return back()
                            ->withErrors($validator)
                            ->withInput();
                    }
                    // 然後把剛剛的結帳方式先寫進 session 內
                    $cart['coPattern'] = $request->input('fPattern');
                    // 寫入最後的步驟
                    $cart['checkoutstep'] = $step;
                    // 更新 session
                    $request->session()->put('cart', $cart);
                }
                // 處理顯示的資料
                $copInfo = CheckoutPattern::where('pattern', $cart['coPattern'])->first();
                $checkoutinfo = [
                    'step'=> $step,
                    'title'=> '第二步 - 填寫基本資料',
                    'pattern'=> $cart['coPattern'],
                    'fee'=> $copInfo->fee,
                    'cashtype'=> $copInfo->cashType,
                    'isRAddr'=> $copInfo->isRAddr,
                ];
                // 如果付款方式與現金結帳有關
                if($checkoutinfo['cashtype'] == 'cash'){
                    // 先取付款方式
                    $cashers = CheckoutPattern::where('type', 'casher')->get('pattern');
                    // 處理付款方式陣列
                    $casher = array();
                    foreach($cashers as $cs){
                        array_push($casher, $cs->pattern);
                    }
                    // 推進 checkoutinfo 陣列內
                    $checkoutinfo['casher'] = $casher;
                }
                // 如果有送過資料，只是回來修改的話
                if(!empty($cart['orderdata'])){
                    $inputdata = [
                        'name'=> $cart['orderdata']['name'],
                        'phone'=> $cart['orderdata']['phone'],
                        'address'=> $cart['orderdata']['address'],
                        'casher'=> $cart['orderdata']['casher'],
                    ];
                }
                // 否則填入帳號內儲存的資料或留空
                else{
                    $inputdata = [
                        'name'=> (empty(Auth::user()->userRealName)) ? null : Auth::user()->userRealName,
                        'phone'=> (empty(Auth::user()->userPhone)) ? null : Auth::user()->userPhone,
                        'address'=> (empty(Auth::user()->userAddress)) ? null : Auth::user()->userAddress,
                        'casher'=> null,
                    ];
                }
                // 把資料返回給視圖
                return view('frontend.goods.checkout.step2' , compact('bc', 'checkoutinfo', 'inputdata', 'cart'));
                break;
            // 第三步 - 確認資料
            case 3:
                // 如果只是重新整理就不要驗證資料
                if($request->has('clientname') || empty($cart['orderdata'])){
                    // 驗證資料後把資料寫入 $cart['orderdata'] 內
                    // 如果輸入的表單內有 clientcasher 欄位就要取資料驗證正確性
                    if($request->has('clientcasher')){
                        $validData = CheckoutPattern::where('type', 'casher')->get('pattern');
                        foreach($validData as $i=> $val){
                            if($i == 0){
                                $rules[] = $val->pattern;
                            }else{
                                array_push($rules, $val->pattern);
                            }
                        }
                    }else{
                        $rules = [];
                    }
                    // 驗證表單資料
                    $validator = Validator::make($request->all(), [
                        'clientname' => ['required', 'string', 'min:3', 'max:50'],
                        'clientphone' => ['required', 'regex:/(0)[0-9]{9}/', 'max:20'],
                        'clientaddress' => ['required', 'string', 'min:3', 'max:100'],
                        // 僅有超商取付沒有這欄位
                        'clientcasher' => ['sometimes', 'required', 'string', Rule::in($rules)],
                    ]);
                    // 若驗證失敗
                    if ($validator->fails()) {
                        // 針對錯誤訊息新增一欄訊息類別
                        $validator->errors()->add('msg', $rules)->add('type', 'error');
                        return back()
                            ->withErrors($validator)
                            ->withInput();
                    }
                    // 沒有錯誤就儲存資料進 session
                    $cart['orderdata'] = [
                        'name'=> $request->input('clientname'),
                        'phone'=> $request->input('clientphone'),
                        'address'=> $request->input('clientaddress'),
                        'casher'=> (empty($request->input('clientcasher'))) ? null : $request->input('clientcasher'),
                    ];
                    // 儲存最後結帳步驟
                    $cart['checkoutstep'] = 3;
                    // 更新 session 值
                    $request->session()->put('cart', $cart);
                }
                // 處理顯示資料
                $copInfo = CheckoutPattern::where('pattern', $cart['coPattern'])->first();
                $checkoutinfo = [
                    'step'=> $step,
                    'title'=> '第三步 - 確認資料',
                    'pattern'=> $cart['coPattern'],
                    'fee'=> $copInfo->fee,
                    'isRAddr'=> $copInfo->isRAddr,
                    'total'=> $cart['total'],
                    'cart'=> $cart['goods'],
                    'gooddata'=> Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsName', 'goodsPrice']),
                ];
                $orderdata = [
                    'name'=> $cart['orderdata']['name'],
                    'phone'=> $cart['orderdata']['phone'],
                    'address'=> $cart['orderdata']['address'],
                    'casher'=> $cart['orderdata']['casher'],
                ];
                return view('frontend.goods.checkout.step3', compact('bc', 'checkoutinfo', 'orderdata'));
                break;
            // 第四步 - 完成下訂
            case 4:
                // 前面資料都驗證過了，不需要再驗證
                // 訂單序號
                $serial = hexdec(uniqid());
                // 取得運費和付款方式
                $cData = CheckoutPattern::where('pattern', $cart['coPattern'])->first();
                // 寫入訂單基本資料
                $orders = Orders::create([
                    'orderSerial'=> $serial,
                    'orderMember'=> Auth::user()->userName,
                    'orderRealName'=> $cart['orderdata']['name'],
                    'orderPhone'=> $cart['orderdata']['phone'],
                    'orderAddress'=> $cart['orderdata']['address'],
                    'orderPrice'=> $cart['total'] + $cData->fee,
                    'orderCasher'=> (empty($cart['orderdata']['casher'])) ? null : $cart['orderdata']['casher'],
                    'orderPattern'=> $cart['coPattern'],
                    'orderFreight'=> $cData->fee,
                    'orderStatus'=> ($cData->cashType == 'cash') ? "等待付款" : "等待出貨",
                ]);
                // 先取得各商品單價
                $prices = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsOrder', 'goodsPrice']);
                // 把各商品單價儲存下來(0: 數量, 1: 單價)
                $ordercontent = [];
                foreach($prices as $price){
                    array_push($ordercontent,[
                        // 最後插入的 ID 就是對應的訂單編號
                        'orderID'=> $orders->orderID,
                        // 這次循環的 goodsOrder 就是商品編號
                        'goodID'=> $price->goodsOrder,
                        // 商品數量從購物車取
                        'goodQty'=> $cart['goods'][$price->goodsOrder],
                        // 商品價格
                        'goodPrice'=> $price->goodsPrice,
                    ]);
                }
                // 寫入訂單內容（商品）
                OrderDetail::insert($ordercontent);
                // 如果有把「儲存資料」打勾就也要更新會員資料
                if(!empty($request->input('savedata'))){
                    User::where('userName', Auth::user()->userName)->update([
                        'userRealName' => $cart['orderdata']['name'],
                        'userPhone'=> $cart['orderdata']['phone'],
                        'userAddress'=> ($cData->isRAddr == 'true') ? $cart['orderdata']['address'] : null,
                    ]);
                }
                // 處理顯示資料
                $result = [
                    'serial'=> $serial,
                    'total'=> $cart['total'] + $cData->fee,
                ];
                $checkoutinfo = [
                    'title'=> '完成訂單！',
                    'step'=> 4,
                ];
                // 刪除購物車內容
                $request->session()->forget('cart');
                // 清除儲存的購物車
                User::find(Auth::user()->uid)->sessions()->where('sessionID', $request->cookie('loginSession'))->update([
                    'savedCart'=> null,
                    'savedTotal'=> null,
                ]);
                return view('frontend.goods.checkout.step4', compact('bc', 'result', 'checkoutinfo'));
                break;
            default:
                return back()->withErrors([
                    'msg'=> '請依正常程序結帳',
                    'type'=> 'error',
                ]);
        }
    }

    /**
     * [AJAX] 加入購物車
     * @param Request $request Request 實例
     * @return JSON Json 回應購物車內商品加總的金額或錯誤訊息
     */
    public function joincart(Request $request)
    {
        // 站外結帳直接拒絕要求
        if(!empty($request->session()->get('cart')['ecpaycheckout'])){
            return response()->json(['error'=> '未完成站外結帳不可修改購物車內容！'], 400);
        }
        $validator = Validator::make($request->all(),[
            'goodid' => ['required', 'numeric', 'min:1'],
        ]);
        // 若驗證失敗就給錯誤訊息，否則設定 $gid 變數
        if ($validator->fails()) {
            // 返回 json 格式錯誤訊息
            return response()->json(['error'=> '您的商品 ID 不正確，請依正常程序將商品加入購物車！'], 400);
        }else{
            $gid = (int)$request->input('goodid');
        }
        // 如果找不到商品編號就返回錯誤訊息
        if(Goods::where('goodsOrder', $gid)->count() < 1){
            return response()->json(['error'=> '找不到該商品！'], 400);
        }
        // 否則就把商品加進購物車
        // 如果購物車內已經有商品了
        if(! empty($request->session()->get('cart')['goods']) ){
            /**
             * 從 session 取出來的購物車變數
             * @param array $cart['goods'] [$goodID=>$qty] 購物車內商品 ID 的陣列
             * @param int $cart['total'] 購物車內商品的總金額
             * @var array $cart 購物車內容
             */
            $cart = $request->session()->get('cart');
            // 如果在陣列內就更新數量
            if(! empty($cart['goods'][$gid])){
                // 更新數量
                $cart['goods'][$gid] += 1;
            }
            // 不在陣列內就推進去
            else{
                $cart['goods'][$gid] = 1;
            }
        }
        // 購物車不存在就建立購物車
        else{
            // 直接建立購物車並放入商品
            $cart['goods'] = [
                $gid=> 1,
            ];
        }
        // 從資料庫取得購物車內各商品 ID 的價格
        $prices = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsOrder', 'goodsPrice']);
        // 計算目前總價格
        $total = 0;
        foreach($prices as $price){
            // 計算總價格（總金額 += 數量 * 單價）
            $total += ($cart['goods'][$price->goodsOrder] * $price->goodsPrice);
        }
        // 將值寫入 session 中方便之後取資料
        $cart['total'] = $total;
        // 寫 session
        $request->session()->put('cart', $cart);
        // 返回 json 回應給 ajax
        return response()->json(['data'=> $cart['total']], 200);
    }

    /**
     * [AJAX] 變更購物車商品數量
     * @param Request $request Request 實例
     * @return JSON AJAX 會返回 Json 回應變更後的相關資料或錯誤訊息
     */
    public function modifyQty(Request $request)
    {
        // 站外結帳直接拒絕要求
        if(!empty($request->session()->get('cart')['ecpaycheckout'])){
            return response()->json(['error'=> '未完成站外結帳不可修改購物車內容！'], 400);
        }
        // 驗證表單訊息
        $validator = Validator::make($request->all(),[
            'gid' => ['required', 'numeric', 'min:1'],
            'qty' => ['required', 'numeric', 'min:1'],
        ]);
        // 若驗證失敗就給錯誤訊息，否則設定 $gid 變數
        if ($validator->fails()) {
            // 把錯誤訊息處理起來方便返回 JSON 字串
            foreach($validator->errors()->all() as $i=> $msg){
                if($i == 0){
                    $errormsg = $msg;
                }else{
                    $errormsg += "\n$msg";
                }
            }
            // 返回 json 格式錯誤訊息
            return response()->json(['error'=> $errormsg], 400);
        }
        $gid = (int)$request->input('gid');
        $qty = (int)$request->input('qty');
        $cart = $request->session()->get('cart');
        // 如果購物車內有這個商品
        if(!empty($cart['goods'][$gid])){
            // 更新數量
            $cart['goods'][$gid] = $qty;
            // 取得商品單價以便更新小計和總額
            $prices = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsOrder', 'goodsPrice']);
            $total = 0;
            $subtotal = 0;
            foreach($prices as $price){
                // 如果循環到的商品編號是這次更新的編號就計算小計金額
                if($price->goodsOrder == $gid){
                    $subtotal = $cart['goods'][$price->goodsOrder] * $price->goodsPrice;
                }
                // 計算總金額
                $total += $cart['goods'][$price->goodsOrder] * $price->goodsPrice;
            }
            $cart['total'] = $total;
            // 寫入 session
            $request->session()->put('cart', $cart);
            // 返回 JSON 回應
            return response()->json(['gid'=> $gid, 'subtotal'=> $subtotal, 'total'=> $total], 200);
        }
        // 不在購物車內就給錯誤訊息
        else{
            return response()->json(['error'=> '請勿使用非正規方式更新購物車'], 400);
        }
    }

    /**
     * [AJAX] 移除購物車項目
     * @param Request $request Request 實例
     * @return JSON AJAX 會返回 Json 回應移除購物車項目後的金額或錯誤訊息
     */
    public function removeItem(Request $request)
    {
        // 站外結帳直接拒絕要求
        if(!empty($request->session()->get('cart')['ecpaycheckout'])){
            return response()->json(['error'=> '未完成站外結帳不可修改購物車內容！'], 400);
        }
        // 驗證表單訊息
        $validator = Validator::make($request->all(),[
            'gid' => ['required', 'numeric', 'min:1'],
        ]);
        // 若驗證失敗就給錯誤訊息，否則設定 $gid 變數
        if ($validator->fails()) {
            // 把錯誤訊息處理起來方便返回 JSON 字串
            foreach($validator->errors()->all() as $i=> $msg){
                if($i == 0){
                    $errormsg = $msg;
                }else{
                    $errormsg += "\n$msg";
                }
            }
            // 返回 json 格式錯誤訊息
            return response()->json(['error'=> $errormsg], 400);
        }
        $gid = (int)$request->input('gid');
        $cart = $request->session()->get('cart');
        // 如果購物車內沒有這個項目
        if(empty($cart['goods'][$gid])){
            return response()->json(['error'=> '購物車內沒有該商品，請依正常程序將商品從購物車移除！'], 400);
        }
        // 先取得該商品的單價
        $price = Goods::where('goodsOrder', $gid)->value('goodsPrice');
        // 更新購物車內總金額
        $cart['total'] -= $price * $cart['goods'][$gid];
        // 移除商品
        unset($cart['goods'][$gid]);
        // 計算剩餘商品數
        $itemnums = count($cart['goods']);
        // 設定總額
        $total = $cart['total'];
        // 更新 session 內的值
        $request->session()->put('cart', $cart);
        // 返回 JSON 回應
        return response()->json(['gid'=> $gid, 'cartnums'=> $itemnums, 'total'=> $total], 200);
    }

    /**
     * [AJAX] 儲存購物車
     * @param Request $request Request 實例
     * @return JSON AJAX 會返回 Json 回應購物車重置後的金額或錯誤訊息
     */
    public function savecart(Request $request)
    {
        // 如果購物車是空的就給錯誤訊息
        if(empty($request->session()->get('cart')['goods'])){
            return Response()->json(['error'=> '您的購物車是空的，請先將商品加入購物車後再試一次！'], 400);
        }
        // 沒問題就開始準備寫入資料庫
        $cart = $request->session()->get('cart');
        User::find(Auth::user()->uid)->sessions()->update([
            'savedCart'=> json_encode($cart['goods']),
            'savedTotal'=> $cart['total'],
        ]);
        return response()->json(['msg'=> '儲存購物車成功！'], 200);
    }

    /**
     * [AJAX / 表單 POST] 執行重置購物車
     * @param Request $request Request 實例
     * @return JSON AJAX 會返回 Json 回應購物車重置後的金額或錯誤訊息
     * @return Redirect 表單 POST 則會返回 redirect 實例
     */
    public function resetcart(Request $request)
    {
        // 站外結帳直接拒絕要求
        if(!empty($request->session()->get('cart')['ecpaycheckout'])){
            return response()->json(['error'=> '未完成站外結帳不可修改購物車內容！'], 400);
        }
        // 如果不是 AJAX
        if (! $request->expectsJson()) {
            // 清掉整個購物車 session
            $request->session()->forget('cart');
            // 清掉資料庫儲存的購物車資料
            User::find(Auth::user()->uid)->sessions()->update([
                'savedCart'=> null,
                'savedTotal'=> null,
            ]);
            return redirect(route('goods.viewcart'))->withErrors([
                'msg'=> '重置購物車成功！',
                'type'=> 'success',
            ]);
        }
        // 如果是 AJAX
        else{
            return response()->json(['data'=> 0, 'msg'=> '重置購物車成功！'], 200);
        }
    }
}
