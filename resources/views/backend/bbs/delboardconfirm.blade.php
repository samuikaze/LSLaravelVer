@extends('backend.layouts.master')

@section('title', '確認刪除「' . $bdata->boardName . '」 - 討論板設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form method="POST" action="{{ route('admin.bbs.deleteboard', ['bid'=> $bdata->boardID]) }}" class="form-horizontal">
        @csrf
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>您確定要刪除這個討論區嗎？其下所有文章也會被刪除，且這個動作無法復原！</strong></h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">討論區名稱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $bdata->boardName }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">討論區描述</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $bdata->boardDescript }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">討論區建立時間</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $bdata->boardCTime }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">討論區建立者</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $cuser }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">討論區圖片</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><img src="{{ asset('images/bbs/board/' . $bdata->boardImage) }}" width="100%" /></p>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <input type="submit" name="submit" class="btn btn-danger" value="確認刪除" />
                    <a href="{{ route('admin.bbs.bbs', ['action'=> 'list']) }}" class="btn btn-success">返回列表</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection