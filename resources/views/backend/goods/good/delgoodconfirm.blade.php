@extends('backend.layouts.master')

@section('title', '確認移除' . $gooddata->goodsName . ' - 商品設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form onsubmit="return confirm('此功能會將此商品所有資料移除，會員訂單內的商品圖也可能因此無法顯示\n建議以「暫停販售」功能將商品停止販售\n您仍確定要移除這項商品嗎？');" method="POST" class="form-horizontal" action="{{ route('admin.goods.delgood', ['gid'=> $gooddata->goodsOrder]) }}" style="margin-top: 1em;">
        @csrf
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>您確定要移除這個商品嗎？這個動作無法復原！</strong></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle" style="color: orange;"></i> 此功能會將此商品所有資料移除，會員訂單內的商品圖也會因此無法顯示，建議以「暫停販售」功能將商品停止販售</div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品識別碼</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $gooddata->goodsOrder }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品名稱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $gooddata->goodsName }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品上架時間</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $uptime }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品上架者</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $upuser }} ({{ $gooddata->goodsUp }})</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品販售狀態</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">@if($gooddata->goodsStatus == 'up') 正常販售 @else 暫停販售 @endif</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品價格</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $gooddata->goodsPrice }} 元</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品剩餘庫存量</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $gooddata->goodsQty }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品描述</label>
                    <div class="col-sm-10">
                        <div class="form-control-static">{!! $gooddata->goodsDescript !!}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品圖片</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><img src="{{ asset('images/goods/' . $gooddata->goodsImgUrl) }}" width="100%" /></p>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <input type="submit" name="submit" class="btn btn-danger" value="確認移除" />
                    <a href="{{ route('admin.goods.good', ['action'=> 'list']) }}" class="btn btn-success">返回商品管理</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection