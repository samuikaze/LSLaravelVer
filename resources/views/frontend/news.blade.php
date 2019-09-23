@extends('frontend.layouts.master')

@section('title', '最新消息')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10" style="float:unset; margin: 0 auto;">
                @if (empty($newsData))
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title">錯誤</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h2 class="news-warn">此頁目前沒有可以顯示的消息。<br /><br />
                                <div class="btn-group" role="group">
                                    <a href="news.php" class="btn btn-success">返回第一頁</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                @else
                    <!-- 消息面版v2 -->
                    <div class="tab-content">
                        <!-- 全部公告 -->
                        <table id="news" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>類型</th>
                                    <th>標題</th>
                                    <th>發佈時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($newsData as $news)
                                    <tr>
                                        <td class="newsType">@if ($news->newsType == '一般') <span class="badge badge-primary"> @else <span class="badge badge-success"> @endif {{ $news->newsType }}</span></td>
                                        <td><a href="{{ route('news.detail', ['id'=> $news->newsOrder]) }}">{{ $news->newsTitle }}</a>@if (strtotime("now") - strtotime($news->postTime) <= 604800) &nbsp;&nbsp;<span class="badge badge-warning">NEW!</span> @endif</td>
                                        <td class="releaseTime">{{ $news->postTime }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- 頁數按鈕開始 -->
                    <div class="text-center">
                        <ul class="pagination">
                            @if ($cPage == 1) <li class="disabled"><a aria-label="Previous"> @else <li><a href="{{ route('news', ['page' => $cPage - 1]) }}" aria-label="Previous"> @endif <span aria-hidden="true">«</span></a></li>
                            @for($i = 1; $i <= $tPage; $i++)
                                @if ($cPage == $i) <li class="active"><a href="{{ route('news', ['page' => $i]) }}"> @else <li><a> @endif {{ $i }} @if ($cPage == $i) <span class="sr-only">(current)</span> @endif </a></li>
                            @endfor
                            @if ($cPage == $tPage) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route('news', ['page'=> $cPage + 1]) }}" aria-label="Next"> @endif <span aria-hidden="true">»</span></a></li>
                        </ul>
                    </div>
                    <!-- 頁數按鈕結束 -->
                @endif
            </div>
        </div>
    </div>
@endsection