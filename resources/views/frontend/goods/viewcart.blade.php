@extends('frontend.layouts.master')

@section('title', '檢視購物車')

@section('content')
<div class="container">
    <div class="check">
        <h1>購物車項目 (<span id="itemqty">{{ $cartinfo['nums'] }}</span>)</h1>
        {{-- 若購物車不為空 --}}
        @if($cartinfo['nums'] > 0)
            <div class="col-md-9 cart-items">
                @foreach($cartinfo['goods'] as $item)
                    <!-- 一個購物車項目 -->
                    <div id="anCartItem{{ $item->goodsOrder }}">
                        <div class="cart-header">
                            <div class="close1"><a id="removeitem" data-gid="{{ $item->goodsOrder }}" class="btn btn-warning">×</a></div>
                            <div class="cart-sec simpleCart_shelfItem">
                                <div class="cart-item cyc">
                                    <img src="{{ asset("images/goods/$item->goodsImgUrl") }}" class="img-responsive cartitemimage" alt="" />
                                </div>
                                <div class="cart-item-info">
                                    <h3><a href="{{ route('gooddetail', ['goodId'=> $item->goodsOrder]) }}" class="cartItemTitle">{{ $item->goodsName }}</a><span>{!! $item->goodsDescript !!}</span></h3>
                                    <div class="alert alert-warning" role="alert">
                                        <span class="qty">數量：<input name="goodsQty" id="goodsQty" data-gid="{{ $item->goodsOrder }}" type="number" value="{{ $cartinfo['qty'][$item->goodsOrder] }}" style="width: 6em;" />&nbsp;・&nbsp;單價：NT$ <span id="gPrice{{ $item->goodsOrder }}">{{ $item->goodsPrice }}</span></span>
                                        <span id="gTot{{ $item->goodsOrder }}" class="tot" style="font-size: 1.2em;">小計：NT$ {{ $cartinfo['qty'][$item->goodsOrder] * $item->goodsPrice }}</span>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <hr class="cartitem-margin" />
                    </div>
                    <!-- /一個購物車項目 -->
                @endforeach
            </div>
        {{-- 若購物車為空 --}}
        @else
        <div class="col-md-9">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">訊息</h3>
                </div>
                <div class="panel-body">
                    <h2 class="info-warn">您的購物車為空。<br /><br />
                    <a href="{{ route('goods') }}" class="btn btn-lg btn-success">立即前往選購</a>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!-- 側邊欄 -->
    <div class="col-md-3 cart-total">
        @if(!$cartinfo['ecpay'])
            <a class="btn btn-info btn-block btn-lg" href="{{ route('goods') }}" style="margin-bottom: 1em;">繼續選購</a>
        @endif
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">總額（不含運費）</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="totPanel"><span class="cartPanel">小計</span></div>
                    <div class="totValPanel"><span class="cartPanel">NT$ <span id="ajaxTotal">{{ $cartinfo['total'] }}</span></span></div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        {{-- 站外結帳的時候不能變更購物車 --}}
        @if(!$cartinfo['ecpay'])
            {{-- 結帳步驟為空 --}}
            @if(empty($cartinfo['step']))
                {{-- 購物車無商品時顯示選購鈕 --}}
                @if($cartinfo['nums'] < 1)
                    <a id="submitorder" class="btn btn-success btn-block btn-lg" href="{{ route('goods') }}">立即選購</a>
                    <hr class="cartbtn-margin" />
                {{-- 有商品時才顯示結帳和儲存鈕 --}}
                @else
                    <a id="ecpaysubmit" class="btn btn-success btn-block btn-lg" @if(Auth::user()->userPriviledge != 2) href="{{ route('goods.ecpay.checkout', ['step'=> 1]) }}" @else disabled="disabled" title="您已被禁止下訂訂單" @endif>綠界結帳</a>
                    <a @if(Auth::user()->userPriviledge != 2) href="{{ route('goods.checkout', ['step'=> 1]) }}" @else disabled="disabled" title="您已被禁止下訂訂單" @endif id="submitorder" class="btn btn-success btn-block btn-lg">立即結帳</a>
                    <hr class="cartbtn-margin" />
                    <a id="savecart" class="btn btn-info btn-lg btn-block" title="將購物清單儲存下來">儲存購物車</a>
                @endif
                <form action="{{ route('goods.resetcart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="identify" value="form" />
                    <input type="submit" name="submit" class="btn btn-danger btn-lg btn-block rstcart" value="重置購物車" />
                </form>
            @else
                {{-- 如果 isEcpay 是 true 表示是站外結帳 --}}
                @if($cartinfo['isEcpay'] == true)
                    <a href="{{ route('goods.ecpay.checkout', ['step'=> $cartinfo['step']]) }}" id="submitorder" class="btn btn-success btn-block btn-lg">繼續結帳</a>
                @else
                    <a href="{{ route('goods.checkout', ['step'=> $cartinfo['step']]) }}" id="submitorder" class="btn btn-success btn-block btn-lg">繼續結帳</a>
                @endif
                <hr class="cartbtn-margin" />
                <a id="savecart" class="btn btn-info btn-lg btn-block" title="將購物清單儲存下來">儲存購物車</a>
                <form onsubmit="return confirm('重置購物車會連同結帳狀態一起被重置，您確定嗎？');"action="{{ route('goods.resetcart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cancelorder" value="form" />
                    <input type="submit" id="cancelorder" name="submit" class="btn btn-danger btn-block btn-lg rstcart" value="重置購物車" />
                </form>
            @endif
        @endif
    </div>
</div>
@endsection