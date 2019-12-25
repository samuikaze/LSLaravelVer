@extends('backend.layouts.master')

@section('title', '刪除作品確認 - 作品設定 | 後台管理首頁')

@section('content')
<form method="POST" class="form-horizontal" action="{{ route('admin.article.firedelproduct', ['pid'=> $pdata->prodOrder]) }}" style="margin-top: 1em;">
    @csrf
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>您確定要刪除這筆作品資料嗎？這個動作無法復原！</strong></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">作品編號</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $pdata->prodOrder }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品名稱</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $pdata->prodTitle }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品描述</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $pdata->prodDescript }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品類型</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $pdata->prodType }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品執行平台</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $pdata->prodPlatform }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品位址</label>
                <div class="col-sm-10">
                    @if($pdata->prodPageUrl == '#')
                        <p class="form-control-static"><span style="color: gray;">無作品網址</span></p>
                    @else
                        <p class="form-control-static">{{ $pdata->prodPageUrl }}</p>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品發售日期</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ date("Y-m-d", strtotime($pdata->prodRelDate)) }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品新增日期</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ date('Y-m-d', strtotime($pdata->prodAddDate)) }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">作品視覺圖</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><img src="{{ asset('images/products/' . $pdata->prodImgUrl) }}" width="100%" /></p>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <input type="submit" name="submit" class="btn btn-danger" value="確認刪除" />
                <a href="{{ route('admin.article.product', ['action'=> 'list']) }}" class="btn btn-success">返回作品管理</a>
            </div>
        </div>
    </div>
</form>
@endsection