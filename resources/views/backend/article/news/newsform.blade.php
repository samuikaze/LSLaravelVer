@extends('backend.layouts.master')

@section('title', '最新消息設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 分頁 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a href="#adminNews" class="urlPush" data-url="list" aria-controls="adminNews" role="tab" data-toggle="tab">管理消息</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a href="#postNews" class="urlPush" data-url="add" aria-controls="postNews" role="tab" data-toggle="tab">張貼新消息</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="adminNews">
            {{-- 若目前無消息 --}}
            @if ($info['nums'] == 0)
                <div class="panel panel-info" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">訊息</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="info-warn">目前沒有任何消息！</h2>
                    </div>
                </div>
            {{-- 有消息就顯示 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th class="news-order">序</th>
                            <th class="news-title">消息標題</th>
                            <th class="news-admin">消息管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['data'] as $news)
                            <tr>
                                <td class="news-order">{{ ( ($info['thisPage'] - 1) * $info['listnums']) + $loop->index + 1 }}</td>
                                <td class="news-title">@if($news->newsType == '一般') <span class="badge badge-primary"> @else <span class="badge badge-success">@endif{{ $news->newsType }}</span>&nbsp;{{ $news->newsTitle }}</td>
                                <td class="news-admin"><a href="{{ route('admin.article.editnews', ['newsid'=> $news->newsOrder]) }}" class="btn btn-info">編輯</a><a href="{{ route('admin.article.delnewsconfirm', ['newsid'=> $news->newsOrder]) }}" class="btn btn-danger">刪除</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            {{-- 頁數按鈕 --}}
            @if($info['totalPage'] > 1)
                <div class="text-center">
                    <ul class="pagination">
                        @if ($info['thisPage'] == 1) <li class="disabled"><a aria-label="Previous"> @else <li><a href="{{ route('admin.article.news', ['action'=> $info['action'], 'p' => $info['thisPage'] - 1]) }}" aria-label="Previous"> @endif <span aria-hidden="true">«</span></a></li>
                        @for($i = 1; $i <= $info['totalPage']; $i++)
                            @if ($info['thisPage'] == $i) <li class="active"><a href="{{ route('admin.article.news', ['action'=> $info['action'], 'p' => $i]) }}"> @else <li><a href="{{ route('admin.article.news', ['action'=> $info['action'], 'p' => $i]) }}"> @endif {{ $i }} @if ($info['thisPage'] == $i) <span class="sr-only">(current)</span> @endif </a></li>
                        @endfor
                        @if ($info['thisPage'] == $info['totalPage']) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route('admin.article.news', ['action'=> $info['action'], 'p'=> $info['thisPage'] + 1]) }}" aria-label="Next"> @endif <span aria-hidden="true">»</span></a></li>
                    </ul>
                </div>
            @endif
        </div>
        <!-- 張貼新消息 -->
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="postNews">
            <form method="POST" action="{{ route('admin.article.addnews') }}">
                @csrf
                <div class="form-group">
                    <label for="newsTitle">消息標題</label>
                    <input type="text" class="form-control" name="newsTitle" id="newsTitle" placeholder="請輸入消息標題" />
                </div>
                <div class="form-group">
                    <label for="newsType">消息類型</label>
                    <select name="newsType" class="form-control" id="newsType">
                        <option value="" selected>請選擇類型</option>
                        <option value="normal">一般</option>
                        <option value="info">資訊</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="newsContent">消息內容</label>
                    <textarea id="editor1" name="newsContent" class="form-control noResize" rows="3" placeholder="請輸入消息內容"></textarea>
                    <script>CKEDITOR.replace( 'editor1' );</script>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection