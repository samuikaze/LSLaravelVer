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
            @if($checkoutinfo['step'] == 4) <span> @endif<i class="fas fa-clipboard-check"></i> 完成訂單 @if($checkoutinfo['step'] == 4) </span> @endif
        </h1>
        <hr class="divideBC" />
        <div class="col-md-9 cart-total">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Step 1 - 請選擇您的結帳方式</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('goods.ecpay.checkout', ['step'=> 2]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="fPattern">結帳方式</label>
                            <select class="form-control" name="fPattern" id="fPattern">
                                <option value="">請選擇結帳方式</option>
                                @foreach ($checkoutinfo['pattern'] as $val)
                                    <option value="{{ $val->pattern }}" @if($val->pattern == $checkoutinfo['selected']) selected @endif>{{ $val->pattern }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <a href="{{ route('goods') }}" class="btn btn-lg btn-info">繼續選購</a>
                            <input type="submit" name="submit" class="btn btn-success btn-lg" value="確認結帳" />
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
                        <div class="totPanel"><span class="cartPanel">小計</span></div>
                        <div class="totValPanel"><span class="cartPanel">NT$ <span id="ajaxTotal">{{ $cart['total'] }}</span></span></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection