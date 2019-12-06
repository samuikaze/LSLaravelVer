@extends('frontend.layouts.master')

@section('title', '選擇討論版 | 討論區')

@section('content')
<!-- 討論版塊放置區 -->
<div class="row" style="margin-top: 0px; padding-top: 0px;">
    @foreach($boards as $board)
    <div class="col-md-4 courses-info">
        <div class="thumbnail">
            <a href="#"><img src="{{ asset('images/bbs/board/' . $board->boardImage) }}" style="width: 640px; height: 310px"></a>
            <div class="caption">
                <!--<p class="numbers fRight">文章數 <span>99,999</span></p>-->
                <h3 class="pull-left">{{ $board->boardName }} </h3>
                <div class="fLeft">{!! $board->boardDescript !!}</div>
                <div class="clearfix"></div>
                <p class="text-center">
                    <div class="text-center" style="margin-bottom: 15px;">
                        <a href="{{ route('showboard', ['bid' => $board->boardID]) }}" class="btn btn-block btn-warning">進入討論板</a>
                    </div>
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection