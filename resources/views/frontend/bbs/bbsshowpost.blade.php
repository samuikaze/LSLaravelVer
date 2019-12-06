@extends('frontend.layouts.master')

@section('title', $postinfo['title'] . " - " . $boardinfo['name'] . " | 討論區")

@section('content')
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
@else
    <div class="container-fluid">
        <div class="row">
            <?php /*
            <?php if (!empty($_GET['msg']) && $_GET['msg'] == 'delarticlesuccess') { ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><strong>刪除回文成功！</strong></h4>
                </div>
            <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'editpostsuccess') { ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><strong>修改主貼文成功！</strong></h4>
                </div>
            <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'editarticlesuccess') { ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><strong>修改回文成功！</strong></h4>
                </div>
            <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'addreplysuccess') { ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><strong>張貼回文成功！</strong></h4>
                </div>
            <?php } */?>
            <div class="dropdown pull-right">
                <a href="?action=replypost&postid={{ $postinfo['id'] }}" class="btn btn-success">回覆此文章</a>
            </div>
            @foreach($postDatas as $pd)
                @if($loop->index == 0)
                    @if($pd->postStatus == 2 || $pd->postStatus == 3)
                        <div class="alert alert-danger alert-dismissible fade in col-md-11" role="alert" style="margin-top: 1em;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4><strong>本討論文章已被鎖定！</strong></h4>
                        </div>
                    @endif
                    <div class="col-xs-12 col-sm-12 col-md-12 articles">
                        <!-- 主貼文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                            <div class="postUser">
                                <div class="row">
                                    <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->postUserID]['avator']) }}" class="img-responsive avator" /></div>
                                    <div class="col-md-12 col-xs-6 col-sm-6">
                                        <h3 class="postuid">{{ $username[$pd->postUserID]['nickname'] }}</h3>
                                        <h4 class="postername" style="font-weight: normal;">{{ $pd->postUserID }}</h4>
                                        <!--<p>等級: 100</p>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                            <div class="post">
                                <div class="postControl">
                                    <span class="pull-left poststatus">#0&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->postTime }} @if($pd->postStatus != 0) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->postEdittime }} @endif</span>
                                    <span class="posteditor"><?php /*echo ((!empty($_SESSION['uid']) && $val['postUserID'] == $_SESSION['uid']) || $_SESSION['priv'] >= $priv['settingValue']) ? "<a class=\"post-link\" href=\"?action=editpost&type=post&id=" . $val['postID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">編輯</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class=\"post-link\" href=\"?action=delpost&type=post&id=" . $val['postID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;" : ""; */?>大 中 小</span>
                                </div>
                                @if(!empty($pd->postTitle)) <h2 class="postTitle">{{ $pd->postTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->postContent !!}</p>
                            </div>
                        </div>
                    </div> <!-- 主貼文結束 -->
                    @if(!empty($pd->articleContent))
                        <div class="col-xs-12 col-sm-12 col-md-12 articles">
                            <!-- 第一則回文開始 -->
                            <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                                <div class="postUser">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                        <div class="col-md-12 col-xs-6 col-sm-6">
                                            <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                            <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                            <!--<p>等級: 100</p>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                                <div class="post">
                                    <div class="postControl">
                                        <span class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }} @if($pd->articleStatus != 0) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }} @endif</span>
                                        <span class="posteditor"><?php /*echo ((!empty($_SESSION['uid']) && $val['articleUserID'] == $_SESSION['uid']) || $_SESSION['priv'] >= $priv['settingValue']) ? "<a class=\"post-link\" href=\"?action=editpost&type=article&id=" . $val['articleID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">編輯</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class=\"post-link\" href=\"?action=delpost&type=article&id=" . $val['articleID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;" : ""; */?>大 中 小</span>
                                    </div>
                                    @if(!empty($pd->articleTitle)) <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->articleContent !!}</p>
                                </div>
                            </div>
                        </div> <!-- 第一則回文結束 -->
                    @endif
                @else
                    <div class="col-xs-12 col-sm-12 col-md-12 articles">
                        <!-- 其它則回文開始 -->
                        <div class="col-xs-12 col-sm-12 col-md-2 noPadding">
                            <div class="postUser">
                                <div class="row">
                                    <div class="col-md-12 col-xs-6 col-sm-6"><img src="{{ asset("images/userAvator/" . $username[$pd->articleUserID]['avator']) }}" class="img-responsive avator" /></div>
                                    <div class="col-md-12 col-xs-6 col-sm-6">
                                        <h3 class="postuid">{{ $username[$pd->articleUserID]['nickname'] }}</h3>
                                        <h4 class="postername" style="font-weight: normal;">{{ $pd->articleUserID }}</h4>
                                        <!--<p>等級: 100</p>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-10 noPadding">
                            <div class="post">
                                <div class="postControl">
                                    <span class="pull-left poststatus">#{{ (($page['this'] - 1) * $postinfo['dispnums']) + $loop->index + 1 }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $pd->articleTime }}@if($pd->articleStatus != 0) &nbsp;&nbsp;|&nbsp;&nbsp;編輯於 {{ $pd->articleEdittime }} @endif</span>
                                    <span class="posteditor"><?php /* echo ((!empty($_SESSION['uid']) && $val['articleUserID'] == $_SESSION['uid']) || $_SESSION['priv'] >= $priv['settingValue']) ? "<a class=\"post-link\" href=\"?action=editpost&type=article&id=" . $val['articleID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">編輯</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class=\"post-link\" href=\"?action=delpost&type=article&id=" . $val['articleID'] . "&refbid=$refbid&refpage=$refpage&refpostid=" . $_GET['postid'] . "\">刪除</a>&nbsp;&nbsp;|&nbsp;&nbsp;" : ""; */?>大 中 小</span>
                                </div>
                                @if(!empty($pd->articleTitle)) <h2 class="postTitle">{{ $pd->articleTitle }}</h2><hr class="postHR" /> @endif <p class="postContent">{!! $pd->articleContent !!}</p>
                            </div>
                        </div>
                    </div> <!-- 其它回文結束 -->
                @endif
            @endforeach
            <div class="dropdown pull-right">
                <a href="?action=replypost&postid={{ $postinfo['id'] }}" class="btn btn-success">回覆此文章</a>
            </div>
            <div class="clearfix"></div>
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