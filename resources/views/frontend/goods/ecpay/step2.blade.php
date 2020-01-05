@extends('frontend.layouts.master')

@section('title', $checkoutinfo['title'] . ' | 結帳')

@section('content')
<div class="container">
    <div class="check">
        {{-- 結帳麵包屑 --}}
        <h1 class="orderBreadcrumb">
            @if($checkoutinfo['step'] == 1) <span> @endif<i class="fas fa-check-square"></i> 選擇付款及收貨方式 @if($checkoutinfo['step'] == 1) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 2) <span> @endif<i class="fas fa-scroll"></i> 輸入相關資料 @if($checkoutinfo['step'] == 2) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            <i class="fas fa-check-double"></i> 綠界金流結帳 &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 3) <span> @endif<i class="fas fa-clipboard-check"></i> 完成訂單 @if($checkoutinfo['step'] == 3) </span> @endif
        </h1>
        <hr class="divideBC" />
        <!-- 輸入使用者資料 -->
        <div class="col-md-9 cart-total">
            <div class="alert alert-warning alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4><strong>請注意，按下下一步後將跳轉至綠界金流系統且將不能再修改購物車內容！</strong></h4>
            </div>
            <div class="alert alert-warning alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4><strong>請注意，結帳完後請務必按下「返回特店」按鈕，否則無法完成訂單！</strong></h4>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Step 1 - 輸入下訂資料</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('goods.ecpay.checkout', ['step'=> 3]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="clientname">姓名</label>
                            <input type="text" name="clientname" class="form-control" id="clientname" placeholder="請輸入您的姓名" value="{{ $inputdata['name'] }}" required />
                        </div>
                        <div class="form-group">
                            <label for="clientphone">連絡電話</label>
                            <input type="text" name="clientphone" class="form-control" id="clientphone" placeholder="請輸入您的電話" value="{{ $inputdata['phone'] }}" required />
                        </div>
                        <div class="form-group">
                            <label for="clientaddress">收貨地址</label>
                            <input type="text" name="clientaddress" class="form-control" id="clientaddress" placeholder="請輸入您的收貨地址" value="{{ $inputdata['address'] }}" required />
                        </div>
                        <div class="form-group">
                            <label for="fPattern">取貨方式</label>
                            <p class="col-md-12">{{ $cart['coPattern'] }}</p>
                        </div>
                        <div class="form-group text-center">
                            <a href="{{ route('goods.ecpay.checkout', ['step'=> 1]) }}" class="btn btn-info btn-lg">上一步</a>
                            <input type="submit" name="submit" class="btn btn-success btn-lg" value="下一步" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- 側邊欄 --}}
        <div class="col-md-3 cart-total">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">總額（不含運費）</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="totPanel"><span class="cartPanelSmall">小計</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ <span id="ajaxTotal">{{ $cart['total'] }}</span></span></div>
                        <div class="totPanel"><span class="cartPanelSmall">運費</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ {{ $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                        <hr class="divideTotal" />
                        <div class="totPanel"><span class="cartPanel">總計</span></div>
                        <div class="totValPanel"><span class="cartPanel">NT$ {{ $cart['total'] + $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection