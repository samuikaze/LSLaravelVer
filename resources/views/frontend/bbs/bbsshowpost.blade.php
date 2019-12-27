@extends('frontend.layouts.master')

@section('title', $postinfo['title'] . " - " . $boardinfo['name'] . " | 討論區")

@section('content')
{{-- 指定的頁數取不到文章 --}}
@if($postinfo['exist'] == false)
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">錯誤</h3>
        </div>
        <div class="panel-body text-center">
            <h2 class="news-warn">指定的頁數沒有可以顯示的內容！<br /><br />
                <div class="btn-group" role="group">
                    <a class="btn btn-lg btn-info" onClick="javascript:history.back();">返回上一頁</a>
                    <a href="{{ route('showboard', ['bid' => $boardinfo['id']]) }}" class="btn btn-lg btn-success">返回討論板列表</a>
                </div>
            </h2>
        </div>
    </div>
{{-- 如果指定的頁數有文章就開始處理文章 --}}
@else
    <div class="container-fluid">
        <div class="row">
            {{-- 被鎖定和被刪除的文章不讓回覆文章按鈕可按 --}}
            <div class="dropdown pull-right">
                @auth
                    @if(!in_array($postDatas[0]->postStatus, [2, 3, 4]) && Auth::user()->userPriviledge != 1)
                        <a href="{{ route('bbs.showreplypostform', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) }}" class="btn btn-success">回覆此文章</a>
                    @else
                        <a disabled="disabled" title="您處於禁言狀態或文章已被鎖定" class="btn btn-success">回覆此文章</a>
                    @endif
                @else
                    <a disabled="disabled" title="請先登入" class="btn btn-success">回覆此文章</a>
                @endauth
            </div>
            {{-- 開始跑主文章和回文 --}}
            @foreach($postDatas as $pd)
                {{-- 第一次迴圈要處理主文章和第一則回文 --}}
                @if($loop->index == 0)
                    <div class="col-xs-12 col-sm-12 col-md-12 articles">
                        <!-- 主貼文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                            <div class="postUser">
                                <div class="row">
                                    <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->postUserID]['avator']) }}" class="img-responsive avator" /></div>
                                    <div class="col-md-12 col-xs-6 col-sm-6">
                                        <h3 class="postuid">{{ $username[$pd->postUserID]['nickname'] }}</h3>
                                        <h4 class="postername" style="font-weight: normal;">{{ $pd->postUserID }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                            <div class="post">
                                <div class="postControl">
                                    <span id="ar0" class="pull-left poststatus">#0&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->postTime }} @if($pd->postStatus == 1) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->postEdittime }} @endif</span>
                                    <span class="posteditor">@if(! in_array($postinfo['status'], [2, 3, 4])) @auth @if(Auth::user()->userName == $pd->postUserID) @if(Auth::user()->userPriviledge != 1)<a class="post-link" href="{{ route('bbs.showeditpostform', ['bid'=> $boardinfo['id'],'postid'=> $postinfo['id']]) }}">編輯</a>&nbsp;&nbsp;| @endif&nbsp;&nbsp;@endif @if(Auth::user()->userName == $pd->postUserID || Auth::user()->userPriviledge >= $boardinfo['adminPriv'])<a class="post-link" href="{{ route('bbs.showdelconfirm', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id']]) }}">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;@endif @endauth @endif 大 中 小</span>
                                </div>
                                @if(!empty($pd->postTitle)) <h2 class="postTitle">{{ $pd->postTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->postContent !!}</p>
                            </div>
                        </div>
                    </div> <!-- 主貼文結束 -->
                    {{-- 這則文章有第一則回文且沒有被刪除就顯示 --}}
                    @if(!empty($pd->articleContent) && $pd->articleStatus != 4)
                        <!-- 第一則回文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-12 articles">
                            <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                                <div class="postUser">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                        <div class="col-md-12 col-xs-6 col-sm-6">
                                            <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                            <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                                <div class="post">
                                    <div class="postControl">
                                        <span id="{{ $loop->index+1 }}" class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }} @if($pd->articleStatus == 1) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }} @endif</span>
                                        <span class="posteditor">@auth @if(Auth::user()->userName == $pd->articleUserID) @if(Auth::user()->userPriviledge != 1)<a class="post-link" href="{{ route('bbs.showeditpostform', ['bid'=> $boardinfo['id'],'postid'=> $postinfo['id'], 'type'=> 'reply', 'targetpost'=> $pd->articleID]) }}">編輯</a>&nbsp;&nbsp;| @endif&nbsp;&nbsp;@endif @if(Auth::user()->userName == $pd->articleUserID || Auth::user()->userPriviledge >= $boardinfo['adminPriv'])<a class="post-link" href="{{ route('bbs.showdelconfirm', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id'], 'type'=> 'reply', 'targetpost'=> $pd->articleID]) }}">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;@endif @endauth 大 中 小</span>
                                    </div>
                                    @if(!empty($pd->articleTitle)) <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->articleContent !!}</p>
                                </div>
                            </div>
                        </div>
                        <!-- 第一則回文結束 -->
                    {{-- 如果被刪除了 --}}
                    @elseif(!empty($pd->articleContent) && $pd->articleStatus == 4)
                        <!-- 第一則回文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-12 articles">
                            <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                                <div class="postUser">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                        <div class="col-md-12 col-xs-6 col-sm-6">
                                            <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                            <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                                <div class="post" style="background: darkgray;">
                                    <div class="postControl">
                                        <span id="{{ $loop->index+1 }}" class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }} @if($pd->articleStatus == 1)&nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }}@elseif($pd->articleStatus == 4)&nbsp;&nbsp;|&nbsp;&nbsp;已刪除@endif</span>
                                        <span class="posteditor">大 中 小</span>
                                    </div>
                                    {{-- 如果是板主或管理員就正常顯示，僅把背景變灰色 --}}
                                    @if(Auth::check() && Auth::user()->userPriviledge >= $boardinfo['adminPriv'])
                                        @if(!empty($pd->articleTitle))
                                            <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" />
                                        @endif
                                        <p class="postContent">{!! $pd->articleContent !!}</p>
                                    {{-- 一般使用者就直接顯示貼文被刪除的訊息 --}}
                                    @else
                                        <p class="postContent"><span style="font-weight: bold;">[ 這則回文已被刪除 ]</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- 第一則回文結束 -->
                    @endif
                {{-- 處理其它文章 --}}
                @else
                    @if($pd->articleStatus != 4)
                        <!-- 其它則回文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-12 articles">
                            <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                                <div class="postUser">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                        <div class="col-md-12 col-xs-6 col-sm-6">
                                            <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                            <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                                <div class="post">
                                    <div class="postControl">
                                        <span id="{{ $loop->index+1 }}" class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + $loop->index + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }}@if($pd->articleStatus == 1) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }} @endif</span>
                                        <span class="posteditor">@auth @if(Auth::user()->userName == $pd->articleUserID) @if(Auth::user()->userPriviledge != 1)<a class="post-link" href="{{ route('bbs.showeditpostform', ['bid'=> $boardinfo['id'],'postid'=> $postinfo['id'], 'type'=> 'reply', 'targetpost'=> $pd->articleID]) }}">編輯</a>&nbsp;&nbsp;| @endif&nbsp;&nbsp;@endif @if(Auth::user()->userName == $pd->articleUserID || Auth::user()->userPriviledge >= $boardinfo['adminPriv'])<a class="post-link" href="{{ route('bbs.showdelconfirm', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id'], 'type'=> 'reply', 'targetpost'=> $pd->articleID]) }}">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;@endif @endauth 大 中 小</span>
                                    </div>
                                    @if(!empty($pd->articleTitle)) <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->articleContent !!}</p>
                                </div>
                            </div>
                        </div>
                        <!-- 其它回文結束 -->
                    @else
                        <!-- 其它則回文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-12 articles">
                            <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                                <div class="postUser">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                        <div class="col-md-12 col-xs-6 col-sm-6">
                                            <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                            <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                                <div class="post" style="background: darkgray;">
                                    <div class="postControl">
                                        <span id="{{ $loop->index+1 }}" class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + $loop->index + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }}@if($pd->articleStatus == 1)&nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }}@elseif($pd->articleStatus == 4)&nbsp;&nbsp;|&nbsp;&nbsp;已刪除@endif</span>
                                        <span class="posteditor">大 中 小</span>
                                    </div>
                                    {{-- 如果是板主或管理員就正常顯示，僅把背景變灰色 --}}
                                    @if(Auth::check() && Auth::user()->userPriviledge >= $boardinfo['adminPriv'])
                                        @if(!empty($pd->articleTitle))
                                            <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" />
                                        @endif
                                        <p class="postContent">{!! $pd->articleContent !!}</p>
                                    {{-- 一般使用者就直接顯示貼文被刪除的訊息 --}}
                                    @else
                                        <p class="postContent"><span style="font-weight: bold;">[ 這則回文已被刪除 ]</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- 其它回文結束 -->
                    @endif
                @endif
            @endforeach
            {{-- 被鎖定和被刪除的文章不讓回覆文章按鈕可按 --}}
            <div class="dropdown pull-right">
                @auth
                    @if(!in_array($postDatas[0]->postStatus, [2, 3, 4]) && Auth::user()->userPriviledge != 1)
                        <a href="{{ route('bbs.showreplypostform', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) }}" class="btn btn-success">回覆此文章</a>
                    @else
                        <a disabled="disabled" title="您處於禁言狀態或文章已被鎖定" class="btn btn-success">回覆此文章</a>
                    @endif
                @else
                    <a disabled="disabled" title="請先登入" class="btn btn-success">回覆此文章</a>
                @endauth
            </div>
            <div class="clearfix"></div>
            {{-- 總頁數大於一頁就顯示按鈕 --}}
            @if($page['total'] > 1)
                <!-- 頁數按鈕開始 -->
                <div class="text-center">
                    <ul class="pagination">
                        @if($page['this'] == 1) <li class="disabled"><a aria-label="Previous"> @else <li><a href="{{ route('viewdiscussion', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) . "?p=" . ($page['this'] - 1) }}" aria-label="Previous"> @endif<span aria-hidden="true">«</span></a></li>
                        @for($i = 1; $i <= $page['total']; $i++)
                            {{-- 如果這頁就是這顆按鈕就變顏色 --}}
                            @if($page['this'] == $i) <li class="active"><a>{{ $i }}<span class="sr-only">(current)</span> @else <li><a href="{{ route('viewdiscussion', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) . "?p=" . $i }}">{{ $i }} @endif</a></li>
                        @endfor
                        @if ($page['this'] == $page['total']) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route('viewdiscussion', ['bid' => $boardinfo['id'], 'postid' => $postinfo['id']]) . "?p=" . ($page['this'] + 1) }}" aria-label="Next"> @endif<span aria-hidden="true">»</span></a></li>
                    </ul>
                </div>
                <!-- 頁數按鈕結束 -->
            @endif
        </div>
    </div>
@endif
@endsection