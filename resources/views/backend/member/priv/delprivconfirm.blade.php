@extends('backend.layouts.master')

@section('title', '確認刪除' . $privdata->privName . ' - 會員權限設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form class="form-horizontal" action="{{ route('admin.member.deletepriv', ['privid'=> $privdata->privNum]) }}" method="POST">
        @csrf
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>確定要刪除這個權限嗎？擁有這個權限的會員會全部被更改回一般會員！</strong></h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="privnum" class="col-sm-2 control-label">權限編號</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $privdata->privNum }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="privnum" class="col-sm-2 control-label">權限名稱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $privdata->privName }}</p>
                    </div>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                    <a href="{{ route('admin.member.priv', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection