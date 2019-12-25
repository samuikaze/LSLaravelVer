@extends('backend.layouts.master')

@section('title', '編輯「' . $bdata->boardName . '」 - 討論板設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form method="POST" action="{{ route('admin.bbs.doeditboard', ['bid'=> $bdata->boardID]) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="boardname">討論板名稱</label>
            <input type="text" class="form-control" id="boardname" name="boardname" value="{{ $bdata->boardName }}" />
        </div>
        <div class="form-group">
            <label for="boarddescript">討論板描述</label>
            <textarea type="text" class="form-control noResize" id="boarddescript" name="boarddescript">{!! $bdata->boardDescript !!}</textarea>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="hideboard" value="true" @if($bdata->boardHide == 1) checked @endif /> 隱藏討論板
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="boardimage">討論版圖片</label>
            <input type="file" id="boardimage" name="boardimage" />
            <p class="help-block">建議解析度為 640 × 310</p>
            @if($bdata->boardImage != 'default.jpg')
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="delboardimage" value="true" /> 刪除討論版圖片
                    </label>
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="nowimage">目前討論版圖片</label><br />
            <img src="{{ asset('images/bbs/board/' . $bdata->boardImage) }}" id="nowimage" width="100%" />
        </div>
        <div class="form-group text-center">
            <input type="submit" name="submit" class="btn btn-success" value="送出" />
            <a href="{{ route('admin.bbs.bbs', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
        </div>
    </form>
</div>
@endsection