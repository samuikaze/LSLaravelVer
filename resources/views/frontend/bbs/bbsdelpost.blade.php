@extends('frontend.layouts.master')

@section('title', "確認刪除文章 | 討論區")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-push-1">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">@if($postData['type'] == 'post') 您確定要刪除這篇文章嗎？其下所有回文也會一併被刪除！ @else 您確定要刪除這篇回文嗎？ @endif</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('bbs.delpost', ['bid'=> $boardinfo['id'], 'postid'=> $postinfo['id'], 'type'=> $postData['type'], 'targetpost'=> $postData['id']]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@if($postData['type'] == 'post') 文章標題 @else 回文標題 @endif</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">@if($postData['type'] == 'post') {{ $postData['data']->postTitle }} @else {{ empty($postData['data']->articleTitle) ? "(此回文無標題)" : $postData['data']->articleTitle }} @endif</p>
                            </div>
                        </div>
                        @if($postData['type'] == 'post')
                        <div class="form-group">
                            <label class="col-sm-2 control-label">文章類型</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $postData['data']->postType }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@if($postData['type'] == 'post') 文章內容 @else 回文內容 @endif</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">@if($postData['type'] == 'post') {!! $postData['data']->postContent !!} @else {!! $postData['data']->articleContent !!} @endif</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@if($postData['type'] == 'post') 貼文者 @else 回文者 @endif</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">@if($postData['type'] == 'post') {{ $postData['data']->postUserID }} @else {{ $postData['data']->articleUserID }} @endif&nbsp;(&nbsp;<strong>{{ $postData['usernickname'] }}</strong>&nbsp;)</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@if($postData['type'] == 'post') 貼文時間 @else 回文時間 @endif</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">@if($postData['type'] == 'post') {{ $postData['data']->postTime }} @else {{ $postData['data']->articleTime }} @endif</p>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <input type="submit" name="submit" class="btn btn-danger" value="確認刪除" />
                            <a href="{{ url()->previous() }}" class="btn btn-success">返回討論板</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection