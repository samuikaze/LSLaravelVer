@extends('backend.layouts.master')

@section('title', '輪播' . $cdata->imgID . '刪除確認 - 輪播設定 | 後台管理首頁')

@section('content')
<form method="POST" class="form-horizontal" action="{{ route('admin.article.dodeletecs', ['cid'=> $cdata->imgID]) }}" style="margin-top: 1em;">
    @csrf
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>您確定要刪除這個輪播項目嗎</strong></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">輪播編號</label>
                <div class="col-sm-10">
                    <p class="form-control-static">{{ $cdata->imgID }}</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">輪播描述</label>
                <div class="col-sm-10">
                    {{-- 如果描述是空的 --}}
                    @if(empty($cdata->imgDescript))
                        <p class="form-control-static"><span style="color: gray;">無</span></p>
                    @else
                        <p class="form-control-static">{{ $cdata->imgDescript }}</p>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">輪播指向位址</label>
                <div class="col-sm-10">
                    @if(empty($cdata->imgReferUrl))
                        <p class="form-control-static"><span style="color: gray;">無</span></p>
                    @else
                        <p class="form-control-static">{{ $cdata->imgReferUrl }}</p>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">輪播圖片</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><img src="{{ asset('/images/carousel/' . $cdata->imgUrl) }}" width="100%" /></p>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <input type="submit" name="submit" class="btn btn-danger" value="刪除輪播" />
                <a href="{{ route('admin.article.carousel', ['action'=> 'list']) }}" class="btn btn-success">取消</a>
            </div>
        </div>
    </div>
</form>
@endsection