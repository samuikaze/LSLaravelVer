@extends('frontend.layouts.master')

@section('title', '周邊產品')

@section('content')
    <div class="row">
        <div class="col-md-12 text-right">
            <div class="alert alert-warning wadj" role="alert">
                <div class="ca-r">
                    <div class="cart box_1">
                        <a href="userorder.php?action=viewcart">
                            <h3>
                                <div class="total">
                                    <span id="simpleCart_total" class="simpleCart_total">NT$<?php echo (!empty($_SESSION['cart']))? $_SESSION['cartTotal'] : 0; ?></span>
                                    <i class="fas fa-shopping-cart simpleCart_total"></i>
                                </div>
                            </h3>
                            <p class="simpleCart_total">檢視購物車項目</p>
                        </a>
                    </div>
                </div>
                <div class="clearfix"> </div>
            </div>
        </div>
    </div>
    @foreach($goods as $good)
        <!-- 一個完整商品項 -->
        <div class="col-md-4 courses-info">
            <div class="thumbnail">
                <a href="?action=viewgoodsdetail&goodid={{ $good->goodsOrder }}"><img src={{ asset('images/goods/' . $good->goodsImgUrl) }}></a>
                <div class="caption">
                    <div class="numbers fRight">NT$ <span>{{ $good->goodsPrice }}</span></div>
                    <h3 class="fLeft">{{ $good->goodsName }}</h3>
                    <div class="fLeft">{!! $good->goodsDescript !!}</div>
                    <div class="clearfix"></div>
                    <p class="text-center">
                        <div class="text-center" style="margin-bottom: 15px;">
                            <div class="btn-group" role="group" aria-label="...">
                                <a href="?action=viewgoodsdetail&goodid={{ $good->goodsOrder }}" class="btn btn-success">週邊詳細</a>
                                <a id="goodsjCart{{ $good->goodsOrder }}" data-gid="{{ $good->goodsOrder }}" data-clicked="false" class="btn btn-info joinCart" disabled="disabled">加入購物車</a>
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
        <!-- /一個完整商品項 -->
    @endforeach
@endsection