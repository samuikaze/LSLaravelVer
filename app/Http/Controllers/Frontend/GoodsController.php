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
use App\Models\Ordertemp;
use App\Models\Orderdetailtemp;
use Exception;
use DB;

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
        $page['total'] = ceil(Goods::where('goodsStatus', 'up')->count() / $goodsNum);
        // 取得特定頁面商品
        // 先以頁數算起始值
        $start = $goodsNum * ($page['this'] - 1);
        /**
         * 取資料
         * skip 是 SQL 中 LIMIT a, b 中的 a 值
         * take 是 SQL 中 LIMIT a, b 中的 b 值
         */
        $goods = Goods::where('goodsStatus', 'up')->skip($start)->take($goodsNum)->get();
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
        $gBuilder = Goods::where('goodsOrder', $goodId);
        // 如果找不到商品或商品已停售就踢走
        if($gBuilder->count() == 0 || $gBuilder->value('goodsStatus') != 'up'){
            return redirect(route('goods'))->withErrors([
                'msg' => '找不到商品或商品已停止販售',
                'type' => 'error',
            ]);
        }
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
                // 這個是資料還沒送到綠界時給前端模板判斷用的
                'isEcpay'=> (empty($cart['ecpay'])) ? false : true,
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
        // 檢查購物車內容物是不是有已停止販售的商品
        $goodsdata = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get();
        foreach($goodsdata as $good){
            if($good->goodsStatus != 'up'){
                return back()
                       ->withInput()
                       ->withErrors([
                           'msg'=> '購物車中含有已停售的商品，請先移除後再進行結帳！',
                           'type'=> 'error',
                       ]);
            }
        }
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
     * 綠界結帳
     * @param Request $request Request 實例
     * @param int $step 目前步驟
     * @return redirect 重新導實例
     */
    public function ecpayCheckout(Request $request, $step)
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
        // 檢查購物車內容物是不是有已停止販售的商品
        $goodsdata = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get();
        foreach($goodsdata as $good){
            if($good->goodsStatus != 'up'){
                return back()
                       ->withInput()
                       ->withErrors([
                           'msg'=> '購物車中含有已停售的商品，請先移除後再進行結帳！',
                           'type'=> 'error',
                       ]);
            }
        }
        // 先處理麵包屑
        $bc = [
            ['url' => route('goods'), 'name' => '周邊商品一覽'],
            ['url' => route('goods.viewcart'), 'name' => '檢視購物車'],
            ['url' => route(Route::currentRouteName(), ['step'=> $step]), 'name' => '結帳'],
        ];
        switch($step){
            case 1:
                $checkoutinfo = [
                    'step'=> $step,
                    'title'=> '第一步 - 選擇結帳方式',
                    // 取得結帳方式
                    'pattern'=> CheckoutPattern::where('type', 'freight')->where('cashType', 'cash')->get(),
                    'selected'=> (empty($cart['coPattern'])) ? null : $cart['coPattern'],
                ];
                return view('frontend.goods.ecpay.step1', compact('bc', 'checkoutinfo', 'cart'));
                break;
            case 2:
                // 如果 $request 有 fPattern 這個 input 值就是從第一步過來的
                unset($cart['ecpaycheckout']);
                $request->session()->put('cart', $cart);
                if($request->has('fPattern')){
                    // 先驗證資料
                    $validData = CheckoutPattern::where('type', 'freight')->where('cashType', 'cash')->get(['pattern']);
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
                    // 給前端模板識別是站外結帳用
                    $cart['ecpay'] = true;
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
                // 如果有送過資料，只是回來修改的話
                if(!empty($cart['orderdata'])){
                    $inputdata = [
                        'name'=> $cart['orderdata']['name'],
                        'phone'=> $cart['orderdata']['phone'],
                        'address'=> $cart['orderdata']['address'],
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
                return view('frontend.goods.ecpay.step2' , compact('bc', 'checkoutinfo', 'inputdata', 'cart'));
                break;
            case 3:
                // $temp = DB::table('debug')->where('id', 5)->first();
                // dd(json_decode($temp->content, true));
                // 先驗證表單資料
                $validator = Validator::make($request->all(), [
                    'clientname' => ['required', 'string', 'min:3', 'max:50'],
                    'clientphone' => ['required', 'regex:/(0)[0-9]{9}/', 'max:20'],
                    'clientaddress' => ['required', 'string', 'min:3', 'max:100'],
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
                // 先把資料丟進資料庫暫存
                // 取運費
                $fee = CheckoutPattern::where('pattern', $cart['coPattern'])->first()->fee;
                $serial = hexdec(uniqid());
                // 鎖定購物車的修改
                $cart['ecpaycheckout'] = true;
                $cart['orderserial'] = $serial;
                // 先取得各商品單價
                $prices = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsOrder', 'goodsName', 'goodsPrice']);
                // 把各商品單價儲存下來(0: 數量, 1: 單價)
                $ordercontent = [];
                $goods = [];
                // 這邊順便把要 POST 給綠界的商品資料也一併處理起來
                foreach($prices as $price){
                    // 處理要寫資料庫的商品
                    array_push($ordercontent, [
                        // 最後插入的 ID 就是對應的訂單編號
                        'orderSerial'=> $serial,
                        // 這次循環的 goodsOrder 就是商品編號
                        'goodID'=> $price->goodsOrder,
                        // 商品數量從購物車取
                        'goodQty'=> $cart['goods'][$price->goodsOrder],
                        // 商品價格
                        'goodPrice'=> $price->goodsPrice,
                    ]);

                    // 處理要 POST 給綠界的資料
                    array_push($goods, [
                        'Name' => $price->goodsName,
                        'Price' => (int) $price->goodsPrice,
                        'Currency' => "元",
                        'Quantity' => (int) $cart['goods'][$price->goodsOrder],
                    ]);
                }
                // 寫入訂單資料（訂購資料）
                Ordertemp::create([
                    'orderSerial'=> $serial,
                    'orderMember'=> Auth::user()->userName,
                    'orderRealName'=> $cart['orderdata']['name'],
                    'orderPhone'=> $cart['orderdata']['phone'],
                    'orderAddress'=> $cart['orderdata']['address'],
                    'orderPrice'=> $cart['total'] + $fee,
                    'orderPattern'=> $cart['coPattern'],
                    'orderFreight'=> $fee,
                ]);
                // 寫入訂單內容（商品）
                Orderdetailtemp::insert($ordercontent);
                // 更新 session
                $request->session()->put('cart', $cart);
                // 由於會送資料出去給綠界，跳轉會中斷 PHP 的執行，所以要直接下 save() 方法儲存資料
                $request->session()->save();
                // 然後送資料給綠界
                try {
                    $order = new \ECPay_ALLInOne();
                    // 服務參數
                    $params = $this->getEcpayParam();
                    $order->ServiceURL = $params['ServiceURL'];
                    $order->HashKey = $params['HashKey'];
                    $order->HashIV = $params['HashIV'];
                    $order->MerchantID = $params['MerchantID'];
                    $order->EncryptType = $params['EncryptType'];
        
                    // 基本參數 (請依系統規劃自行調整)
                    $MerchantTradeNo = $serial;
                    // 付款完成通知回傳的網址
                    $order->Send['ReturnURL'] = route('goods.ecpay.process');
                    // 訂單編號
                    $order->Send['MerchantTradeNo'] = $serial;
                    // 交易時間
                    $order->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
                    // 交易金額
                    $order->Send['TotalAmount'] = $cart['total'] + $fee;
                    // 交易描述
                    $order->Send['TradeDesc'] = "test";
                    // 付款方式:全功能
                    $order->Send['ChoosePayment'] = \ECPay_PaymentMethod::ALL;
                    // 客戶完成付款後返回網站的按鈕網址
                    $order->Send['ClientBackURL'] = route('goods.ecpay.checkout', ['step'=> 4]);
                    // 訂單的商品資料（上面處理完了，直接拿來用）
                    $order->Send['Items'] = $goods;
                    // 送資料給綠界
                    $order->CheckOut();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 4:
                // 先判斷有沒有結帳成功，也就是訂單有沒有被寫入資料庫
                if(Orders::where('orderSerial', $cart['orderserial'])->count() == 0){
                    // 結帳未成功就刪除訂單資料
                    Ordertemp::where('orderSerial', $cart['orderserial'])->delete();
                    Orderdetailtemp::where('orderSerial', $cart['orderserial'])->delete();
                    // 清掉訂單序號（重送資料會重新產生）
                    unset($cart['orderserial']);
                    // 解鎖購物車變更
                    unset($cart['ecpaycheckout']);
                    // 更新 session
                    $request->session()->put('cart', $cart);
                    return redirect(route('goods.ecpay.checkout', ['step'=> 2]))->withErrors([
                        'msg'=> '您的結帳未成功，請重新結帳一次！',
                        'type'=> 'error',
                    ]);
                }
                // 如果結帳成功就顯示完成頁面
                else{
                    // 資料寫入由另一個方法處理，這邊就是處理顯示資料就好
                    $fee = CheckoutPattern::where('pattern', $cart['coPattern'])->first()->fee;
                    $result = [
                        'serial'=> $cart['orderserial'],
                        'total'=> $cart['total'] + $fee,
                    ];
                    $checkoutinfo = [
                        'title'=> '完成訂單！',
                        'step'=> 4,
                    ];
                    // 刪除購物車內容
                    $request->session()->forget('cart');
                    // 站內外結帳完成頁都一樣就沿用
                    return view('frontend.goods.checkout.step4', compact('bc', 'result', 'checkoutinfo'));
                }
                break;
        }
        
    }

    /**
     * 處理綠界平台回傳的資料
     */
    public function ecpayReceiveData(Request $request)
    {
        try {
            // 收到綠界科技的付款結果訊息，並判斷檢查碼是否相符
            $services = $this->getEcpayParam();
            $AL = new \ECPay_AllInOne();
            $AL->MerchantID = $services['MerchantID'];
            $AL->HashKey = $services['HashKey'];
            $AL->HashIV = $services['HashIV'];
            // 加密方式
            /**「ECPay_EncryptType::ENC_MD5」為 MD5
             * 「ECPay_EncryptType::ENC_SHA256」為 SHA256 
             */
            $AL->EncryptType = \ECPay_EncryptType::ENC_SHA256;
            $feedback = $AL->CheckOutFeedback();
            // 訂單序號
            $serial = $feedback['MerchantTradeNo'];
            // 取出使用者的資料，正式寫進訂單表中，然後刪除那筆暫存資料
            $orderdata = Ordertemp::where('orderSerial', $serial)->first();
            $ordergoods = Orderdetailtemp::where('orderSerial', $serial)->get();
            // 寫入訂單資料並取得這筆資料插入後的自動增加 ID
            $order = Orders::create([
                'orderSerial'=> $serial,
                'orderMember'=> $orderdata->orderMember,
                'orderRealName'=> $orderdata->orderRealName,
                'orderPhone'=> $orderdata->orderPhone,
                'orderAddress'=> $orderdata->orderAddress,
                // 由於 POST 過來都會是文字格式，要轉成與資料庫相應的格式才不會出錯
                'orderPrice'=> (int)$feedback['TradeAmt'],
                'orderDate'=> date('Y-m-d H:i:s', strtotime($feedback['TradeDate'])),
                'orderCasher'=> "信用卡",
                'orderPattern'=> $orderdata->orderPattern,
                'orderFreight'=> $orderdata->orderFreight,
                'orderStatus'=> "等待出貨",
            ]);
            // 處理要寫資料庫的商品
            $detail = [];
            foreach($ordergoods as $good){
                array_push($detail, [
                    // 最後插入的 ID 就是對應的訂單編號
                    'orderID'=> $order->orderID,
                    // 商品編號
                    'goodID'=> $good->goodID,
                    // 商品數量
                    'goodQty'=> $good->goodQty,
                    // 商品價格
                    'goodPrice'=> $good->goodPrice,
                ]);
            }
            OrderDetail::insert($detail);
            // 然後刪除資料庫中暫存的訂單
            Ordertemp::where('orderSerial', $serial)->delete();
            Orderdetailtemp::where('orderSerial', $serial)->delete();
            // 在網頁端回應 1|OK
            echo '1|OK';
        } catch(Exception $e) {
            echo '0|' . $e->getMessage();
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
        // 如果找不到商品編號貨商品已被停售就返回錯誤訊息
        if(Goods::where('goodsOrder', $gid)->count() == 0 || Goods::where('goodsOrder', $gid)->value('goodsStatus') != 'up'){
            return response()->json(['error'=> '找不到該商品或該商品已停售！'], 400);
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
            $prices = Goods::whereIn('goodsOrder', array_keys($cart['goods']))->get(['goodsOrder', 'goodsPrice', 'goodsStatus']);
            $total = 0;
            $subtotal = 0;
            foreach($prices as $price){
                // 檢查每樣在購物車中的商品是不是都還在正常販售
                if($price->goodsStatus != 'up'){
                    return response()->json(['error'=> '購物車中含有已停售的商品，請移除該商品或重置購物車後重試'], 400);
                }
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

    /**
     * 取得綠界結帳的服務參數
     */
    public function getEcpayParam()
    {
        return array(
            'ServiceURL' => "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5",
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'MerchantID' => '2000132',
            'EncryptType' => '1',
        );
    }
}
