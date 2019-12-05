@extends('frontend.layouts.master')

@section('title', "$boardInfo->boardName | 討論區")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="container-fluid" style="margin: 5px 0;">
                <?php /*
                <?php if (!empty($_GET['msg']) && $_GET['msg'] == 'addnewpostsuccess') { ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>張貼新文章成功！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delposterrtype') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>無法判別貼文的屬性，請依正常程序刪除貼文！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delposterrpostid') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>無法判別貼文的識別碼，請依正常程序刪除貼文！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delposterrnotfound') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>找不到這篇文章，請依正常程序刪除貼文！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delposterrauthfail') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>欲刪除的文章發文者與您的登入身份不符，請依正常程序刪除貼文！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delpostsuccess') { ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>刪除文章成功！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delpostsuccessnopostid') { ?>
                    <div class="alert alert-warning alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>刪除文章成功，但因為無法識別文章 ID ，故跳轉至本頁面。</strong></h4>
                    </div>
                <?php }*/ ?>
                <div class="dropdown pull-right">
                    @if($postNums != 0)
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">全部主題 <span class="caret"></span></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#">綜合討論</a></li>
                            <li><a href="#">板務公告</a></li>
                            <li><a href="#">攻略心得</a></li>
                            <li><a href="#">同人創作</a></li>
                        </ul>
                    @endif
                    <a href="?action=addnewpost&boardid=<?php echo $bid; ?>&refpage=<?php echo $page; ?>" class="btn btn-success">張貼文章</a>
                </div>
            </div>
            @if($postNums != 0)
                <table class="table table-hover" style="vertical-align: middle;">
                    <thead>
                        <tr class="info">
                            <th class="post-nums">回文數</th>
                            <th class="post-title">文章標題</th>
                            <th class="post-time">貼文時間</th>
                            <th class="post-time last-operatime">最後操作時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boardcontents as $post)
                        <tr>
                            {{-- $articleNums 是一個儲存各討論串回文數量的陣列，取值用 laravel foreach 內置的 $loop 變數取次數就好 --}}
                            <td class="post-nums text-left">@if($articleNums[$loop->index] >= $hotpost) <span class="text-danger"><strong>{{ $articleNums[$loop->index] }}</strong> @else <span class="text-info">{{ $articleNums[$loop->index] }} @endif </span></td>
                            <td class="post-title"><a href="{{ route('viewdiscussion', ['bid' => $bid, 'postid' => $post['postID']]) }}"><span class="badge badge-warning">{{ $post['postType'] }}</span> {{ $post['postTitle'] }}</a></td>
                            <td class="post-time">{{ $post['postUserID'] }}<br />{{ $post['postTime'] }}</td>
                            <td class="post-time last-operatime">@if(!empty($post['lastUpdateUserID'])) {{ $post['lastUpdateUserID'] }}<br />{{ $post['lastUpdateTime'] }} @else <span style="color: gray;">目前尚無回覆</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="dropdown pull-right">
                    @if ($postNums != 0)
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">全部主題 <span class="caret"></span></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#">綜合討論</a></li>
                            <li><a href="#">板務公告</a></li>
                            <li><a href="#">攻略心得</a></li>
                            <li><a href="#">同人創作</a></li>
                        </ul>
                    @endif
                    <a href="{{-- URL 記得填 --}}" class="btn btn-success">張貼文章</a>
                </div>
            </div>
            @if($tpage > 1)
                <div class="clearfix"></div>
                <!-- 頁數按鈕開始 -->
                <div class="text-center">
                    <ul class="pagination">
                        @if($page == 1) <li class="disabled"><a  aria-label="Previous">@else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $bid]) . "?p=" . ($page - 1) }}" aria-label="Previous"> @endif<span aria-hidden="true">«</span></a></li>
                        @for($i = 1; $i <= $tpage; $i++)
                            @if($page == $i) <li class="active"><a><span class="sr-only">(current)</span> @else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $bid]) . "?p=" . $i }}"> @endif {{ $i }} </a></li>
                        @endfor
                        @if($page == $tpage) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $bid]) . "?p=" . ($page + 1) }}" aria-label="Next"> @endif<span aria-hidden="true">»</span></a></li>
                    </ul>
                </div>
                <!-- 頁數按鈕結束 -->
            @endif
        @else
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">警告</h3>
                </div>
                <div class="panel-body text-center">
                    <h2 class="news-warn" style="color: #8a6d3b !important;">討論板目前無文章<br /><br />
                        <div class="btn-group" role="group">
                            <a href="{{ route('boardselect') }}" class="btn btn-lg btn-info">返回討論板一覽</a>
                            <a href="?action=addnewpost&boardid=<?php echo $bid; ?>" class="btn btn-lg btn-success">按此張貼新文章</a>
                        </div>
                    </h2>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection