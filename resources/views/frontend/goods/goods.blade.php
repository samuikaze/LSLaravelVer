@extends('frontend.layouts.master')

@section('title', '周邊產品')

@section('content')
<div class="row">
    @auth
        <div class="col-md-12 text-right">
            {{-- 購物車按鈕 --}}
            <div class="alert alert-warning wadj" role="alert">
                <div class="ca-r">
                    <div class="cart box_1">
                        <a href="{{ route('goods.viewcart') }}">
                            <h3>
                                <div class="total">
                                    <span id="simpleCart_total" class="simpleCart_total">NT${{ $cartTotal }}</span>
                                    <i class="fas fa-shopping-cart simpleCart_total"></i>
                                </div>
                            </h3>
                            <p class="simpleCart_total">檢視購物車項目</p>
                        </a>
                    </div>
                </div>
                <div class="clearfix"> </div>
            </div>
            {{-- 儲存購物車按鈕 --}}
            <a id="savecart" title="將購物清單儲存下來" @if($cartTotal == 0) disabled="disabled" data-clicked="true" @endif>
                @if($cartTotal == 0)
                    <div id="btn-savecart" class="btn btn-danger savecart" disabled="disabled" role="alert">
                @else
                    <div id="btn-savecart" class="btn btn-info savecart" role="alert">
                @endif
                    <div class="ca-r">
                        <div class="cart box_1">
                            <h3>
                                <div class="total">
                                    <span id="savecart_text"><i class="fas fa-save"></i></span>
                                </div>
                            </h3>
                            <p>儲存購物車</p>
                        </div>
                    </div>
                    <div class="clearfix"> </div>
                </div>
            </a>
        </div>
    @endauth
    @foreach($goods as $good)
        <!-- 一個完整商品項 -->
        <div class="col-md-4 courses-info">
            <div class="thumbnail">
                <a href="{{ route('gooddetail', ['goodId' => $good->goodsOrder]) }}"><img src={{ asset('images/goods/' . $good->goodsImgUrl) }}></a>
                <div class="caption">
                    <div class="numbers fRight">NT$ <span>{{ $good->goodsPrice }}</span></div>
                    <h3 class="fLeft">{{ $good->goodsName }}</h3>
                    <div class="fLeft">{!! $good->goodsDescript !!}</div>
                    <div class="clearfix"></div>
                    <p class="text-center">
                        <div class="text-center" style="margin-bottom: 15px;">
                            <div class="btn-group" role="group" aria-label="...">
                                <a href="{{ route('gooddetail', ['goodId' => $good->goodsOrder]) }}" class="btn btn-success">週邊詳細</a>
                                @auth
                                    <a id="goodsjCart{{ $good->goodsOrder }}" data-gid="{{ $good->goodsOrder }}" data-clicked="false" @if(!$ecpay) class="btn btn-info joinCart" @else class="btn btn-info" disabled="disabled" title="完成站外結帳前此功能不可使用" @endauth>加入購物車</a>
                                @else
                                    <a id="goodsjCart{{ $good->goodsOrder }}" data-gid="{{ $good->goodsOrder }}" data-clicked="false" class="btn btn-info" disabled="disabled" title="此功能登入後才可使用">加入購物車</a>
                                @endauth
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
        <!-- /一個完整商品項 -->
    @endforeach
</div>

<!-- 頁數按鈕開始 -->
<div class="text-center">
    <ul class="pagination">
        @if ($page['this'] == 1) <li class="disabled"> @else <li> @endif <a @if ($page['this'] != 1) href="{{ route('goods', ['p' => ($page['this'] - 1)]) }}" @endif aria-label="Previous"><span aria-hidden="true">«</span></a></li>
        @for($i = 1; $i <= $page['total']; $i++)
            @if ($page['this'] == $i) <li class="active"> @else <li> @endif <a @if ($page['this'] != $i) href="{{ route('goods', ['p' => $i]) }}" @endif >{{ $i }} @if ($page['this'] == $i) <span class="sr-only">(current)</span> @endif</a></li>
        @endfor
        @if ($page['this'] == $page['total']) <li class="disabled"> @else <li> @endif <a @if($page['this'] != $page['total']) href="{{ route('goods', ['p' => ($page['this'] + 1)]) }}" @endif aria-label="Next"><span aria-hidden="true">»</span></a></li>
    </ul>
</div>
<!-- 頁數按鈕結束 -->

@endsection