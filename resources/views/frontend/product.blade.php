@extends('frontend.layouts.master')

@section('title', '作品一覽')

@section('content')
    @if($product->isEmpty())
        <div class="container">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">資訊</h3>
                </div>
                <div class="panel-body text-center">
                    <h2 class="warning-warn">目前尚無作品可顯示。<br /><br />
                    </h2>
                </div>
            </div>
        </div>
    @else
        @foreach($product as $pd)
            <!-- 一個作品項目 -->
            <div class="col-md-6 courses-info" style="margin-bottom: 1em;">
                <div class="prodLists thumbnail">
                    <a data-fancybox href="{{ asset('images/products/' . $pd->prodImgUrl) }}"><img src="{{ asset('images/products/' . $pd->prodImgUrl) }}"></a>
                    <div class="prodText">
                        <h3 class="fLeft prodTitle">{{ $pd->prodTitle }}</h3>
                        <div class="fLeft">
                            <p>{!! $pd->prodDescript !!}</p>
                            <hr class="fLeft prodDivide" />
                            <div class="col-md-6 col-xs-12 pull-left noPadding">
                                <p>類型：{{ $pd->prodType }}</p>
                            </div>
                            <div class="col-md-6 col-xs-12 fRight noPadding">
                                <p>平台：{{ $pd->prodPlatform }}</p>
                            </div>
                            <hr class="fLeft prodDivide" />
                            <div class="col-md-12 col-xs-12 relDate noPadding">
                                @if($reldate == '發售日未定')
                                    <p>發售日：<span style="color: gray;">{{ $reldate[$loop->index] }}</span></p>
                                @else
                                    <p>發售日：{{ $reldate[$loop->index] }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="text-left goProd">
                            <a href="{{ $pd->prodPageUrl }}" class="btn btn-block btn-success">前往頁面</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- /一個作品項目 -->
        @endforeach
    @endif
@endsection