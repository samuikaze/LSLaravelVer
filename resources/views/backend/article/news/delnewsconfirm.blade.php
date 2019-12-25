@extends('backend.layouts.master')

@section('title', '確認刪除消息 - 最新消息設定 | 後台管理首頁')

@section('content')
<form method="POST" class="form-horizontal" action="{{ route('admin.article.firedelnews', ['newsid'=> $ndata->newsOrder]) }}" style="margin-top: 1em;">
    @csrf
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>您確定要刪除這則消息嗎？這個動作無法復原！</strong></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">消息標題</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $ndata->newsTitle }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">消息類型</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><span @if($ndata->newsType == '一般') class="badge badge-primary" @else class="badge badge-success"@endif>{{ $ndata->newsType }}</span></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">消息張貼時間</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $ndata->postTime }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">消息張貼者</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $poster }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">消息內容</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{!! $ndata->newsContent !!}</p>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <input type="submit" name="submit" class="btn btn-danger" value="確認刪除" />
                <a href="{{ route('admin.article.news', ['action'=> 'list']) }}" class="btn btn-success">返回列表</a>
            </div>
        </div>
    </div>
</form>
@endsection