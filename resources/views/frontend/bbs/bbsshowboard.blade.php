@extends('frontend.layouts.master')

@section('title', $boardinfo['name'] . " | 討論區")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="container-fluid" style="margin: 5px 0;">
                <div class="dropdown pull-right">
                    {{-- 有文章才顯示分類按鈕 --}}
                    @if($postNums != 0)
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">全部主題 <span class="caret"></span></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#">綜合討論</a></li>
                            <li><a href="#">板務公告</a></li>
                            <li><a href="#">攻略心得</a></li>
                            <li><a href="#">同人創作</a></li>
                        </ul>
                    @endif
                    <a href="{{ route('bbs.showcreatepostform', ['bid' => $boardinfo['id']]) }}" class="btn btn-success">張貼文章</a>
                </div>
            </div>
            {{-- 如果討論板有文章就開始處理顯示文章 --}}
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
                        {{-- 凱始跑文張烈表 --}}
                        @foreach($boardcontents as $post)
                            {{-- 文章未被刪除 或 是板主 就顯示文章 --}}
                            @if($post['postStatus'] != 4 || (Auth::check() && Auth::user()->userPriviledge >= $boardinfo['adminPriv']))
                                <tr>
                                    {{-- $articleNums 是一個儲存各討論串回文數量的陣列，取值用 laravel foreach 內置的 $loop 變數取次數就好 --}}
                                    <td class="post-nums text-left">@if($articleNums[$loop->index] >= $hotpost) <span class="text-danger"><strong>{{ $articleNums[$loop->index] }}</strong> @else <span class="text-info">{{ $articleNums[$loop->index] }} @endif </span></td>
                                    <td class="post-title"><a href="{{ route('viewdiscussion', ['bid' => $boardinfo['id'], 'postid' => $post['postID']]) }}"><span class="badge badge-warning">{{ $post['postType'] }}</span> {{ $post['postTitle'] }}</a></td>
                                    <td class="post-time">{{ $post['postUserID'] }}<br />{{ $post['postTime'] }}</td>
                                    {{-- 文章狀態只要是已被刪除就通通顯示為刪除 --}}
                                    @if($post['postStatus'] != 4)
                                        <td class="post-time last-operatime">@if($articleNums[$loop->index] > 0) {{ $post['lastUpdateUserID'] }}<br />{{ $post['lastUpdateTime'] }} @else <span style="color: gray;">目前尚無回覆</span> @endif</td>
                                    @else
                                        <td class="post-time last-operatime"><span style="color: gray;">文章已被刪除</span></td>
                                    @endif
                                </tr>
                            {{-- 否則就顯示為刪除 --}}
                            @else
                                <tr>
                                    <td class="post-nums text-left"><span class="text-info">-</span></td>
                                    <td class="post-title"><span class="badge badge-warning">{{ $post['postType'] }}</span> <span style="color: gray;">[ 此文章已被刪除 ]</span></td>
                                    <td class="post-time">{{ $post['postUserID'] }}<br />{{ $post['postTime'] }}</td>
                                    <td class="post-time last-operatime"><span style="color: gray;">文章已被刪除</span></td>
                                </tr>
                            @endif
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
                    <a href="{{ route('bbs.showcreatepostform', ['bid' => $boardinfo['id']]) }}" class="btn btn-success">張貼文章</a>
                </div>
            </div>
            {{-- 總頁數大於一頁就顯示頁數按鈕 --}}
            @if($page['total'] > 1)
                <div class="clearfix"></div>
                <!-- 頁數按鈕開始 -->
                <div class="text-center">
                    <ul class="pagination">
                        @if($page['this'] == 1) <li class="disabled"><a  aria-label="Previous">@else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $boardinfo['id']]) . "?p=" . ($page['this'] - 1) }}" aria-label="Previous"> @endif<span aria-hidden="true">«</span></a></li>
                        @for($i = 1; $i <= $page['total']; $i++)
                            @if($page['this'] == $i) <li class="active"><a><span class="sr-only">(current)</span> @else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $boardinfo['id']]) . "?p=" . $i }}"> @endif {{ $i }} </a></li>
                        @endfor
                        @if($page['this'] == $page['total']) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route(Route::currentRouteName(), ['bid' => $boardinfo['id']]) . "?p=" . ($page['this'] + 1) }}" aria-label="Next"> @endif<span aria-hidden="true">»</span></a></li>
                    </ul>
                </div>
                <!-- 頁數按鈕結束 -->
            @endif
        {{-- 討論板如果沒有任何文章就顯示警示訊息 --}}
        @else
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">警告</h3>
                </div>
                <div class="panel-body text-center">
                    <h2 class="news-warn" style="color: #8a6d3b !important;">討論板目前無文章<br /><br />
                        <div class="btn-group" role="group">
                            <a href="{{ route('boardselect') }}" class="btn btn-lg btn-info">返回討論板一覽</a>
                            <a href="{{ route('bbs.showcreatepostform', ['bid' => $boardinfo['id']]) }}" class="btn btn-lg btn-success">按此張貼新文章</a>
                        </div>
                    </h2>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection