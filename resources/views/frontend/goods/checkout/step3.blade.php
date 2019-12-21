@extends('frontend.layouts.master')

@section('title', $checkoutinfo['title'] . ' | 結帳')

@section('content')
<div class="container">
    <div class="check">
        {{-- 結帳麵包屑 --}}
        <h1 class="orderBreadcrumb">
            @if($checkoutinfo['step'] == 1) <span> @endif<i class="fas fa-check-square"></i> 選擇付款及收貨方式 @if($checkoutinfo['step'] == 1) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 2) <span> @endif<i class="fas fa-scroll"></i> 輸入相關資料 @if($checkoutinfo['step'] == 2) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 3) <span> @endif<i class="fas fa-check-double"></i> 確認資料 @if($checkoutinfo['step'] == 3) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 4) <span> @endif<i class="fas fa-clipboard-check"></i> 完成訂單 @if($checkoutinfo['step'] == 4) </span> @endif
        </h1>
        <hr class="divideBC" />
        <div class="col-md-9 cart-total">
            <form onsubmit="return confirm('資料一經送出後不受理修改資料，您確定要送出資料嗎？');" action="{{ route('goods.checkout', ['step'=> 4]) }}" method="POST">
                @csrf
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">確認您的購物清單</h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning" role="alert">如您在結帳過程中曾經更新過購物清單，請重新整理本頁顯示最新的資訊</div>
                        <table class="table table-striped">
                            <thead>
                                <tr class="warning">
                                    <th>商品名稱</th>
                                    <th>選購數量</th>
                                    <th>單價</th>
                                    <th>小計</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkoutinfo['cart'] as $cart)
                                    <!-- 一個商品 -->
                                    <tr>
                                        <td>{{ $checkoutinfo['gooddata'][$loop->index]->goodsName }}</td>
                                        <td>{{ $cart }}</td>
                                        <td>{{ $checkoutinfo['gooddata'][$loop->index]->goodsPrice }}</td>
                                        <td>{{ $cart * $checkoutinfo['gooddata'][$loop->index]->goodsPrice }} 元</td>
                                    </tr>
                                    <!-- /一個商品 -->
                                @endforeach
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="checkout-lasttext">運費</td>
                                    <td>{{ $checkoutinfo['fee'] }} 元</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="checkout-lasttext">總計</td>
                                    <td>{{ $checkoutinfo['total'] + $checkoutinfo['fee'] }} 元</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">確認您的結帳個人資料</h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning" role="alert">以下資料將會用於出貨與取貨的基本資料，若內容有問題此次訂單將會被取消，請特別留意。</div>
                        <div class="form-group">
                            <label for="fPattern">姓名</label>
                            <div class="col-sm-12">
                                <p class="form-control-static">{{ $orderdata['name'] }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fPattern">電話</label>
                            <div class="col-sm-12">
                                <p class="form-control-static">{{ $orderdata['phone'] }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fPattern">@if($checkoutinfo['isRAddr'] == "true") 收貨地址 @else 最近的郵局或超商名稱 @endif</label>
                            <div class="col-sm-12">
                                <p class="form-control-static">{{ $orderdata['address'] }}</p>
                            </div>
                        </div>
                        {{-- 如果有付款方式的資料就顯示出來 --}}
                        @if (!empty($orderdata['casher']))
                            <div class="form-group">
                                <label for="clientcasher">付款方式</label>
                                <div class="col-sm-12">
                                    <p class="form-control-static">{{ $orderdata['casher'] }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="fPattern">取貨方式</label>
                            <div class="col-sm-12">
                                <p class="form-control-static">{{ $checkoutinfo['pattern'] }}</p>
                            </div>
                        </div>
                        @if (empty(Auth::user()->userRealName) && empty(Auth::user()->userPhone) && empty(Auth::user()->userAddress))
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="savedata" value="true" />
                                    將資料儲存至我的帳號資料內
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group text-center">
                    <div class="btn-group btn-group-lg text-center" role="group">
                        <a href="{{ route('goods.checkout', ['step'=> 2]) }}" class="btn btn-info">返回修改</a>
                        <input type="submit" name="submit" class="btn btn-success" value="確認無誤" />
                    </div>
                </div>
            </form>
        </div>
        {{-- 側邊欄 --}}
        <div class="col-md-3 cart-total">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">總額</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="totPanel"><span class="cartPanelSmall">小計</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ <span id="ajaxTotal">{{ $checkoutinfo['total'] }}</span></span></div>
                        <div class="totPanel"><span class="cartPanelSmall">運費</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ {{ $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                        <hr class="divideTotal" />
                        <div class="totPanel"><span class="cartPanel">總計</span></div>
                        <div class="totValPanel"><span class="cartPanel">NT$ {{ $checkoutinfo['total'] + $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection