@extends('backend.layouts.master')

@section('title', '輪播' . $cdata->imgID . ' - 輪播設定 | 後台管理首頁')

@section('content')
<form action="{{ route('admin.article.doeditcs', ['cid'=> $cdata->imgID]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="carouselDescript">輪播描述</label>
        <input type="text" name="carouselDescript" id="carouselDescript" class="form-control" placeholder="請輸入顯示於輪播圖下方的描述文字，不填可留空" value="{{ $cdata->imgDescript }}" />
    </div>
    <div class="form-group">
        <label for="carouselTarget">輪播位址</label>
        <input type="text" name="carouselTarget" id="carouselTarget" class="form-control" placeholder="請輸入當按下輪播圖時欲跳轉的位址，不填可留空" value="{{ $cdata->imgReferUrl }}" />
    </div>
    <div class="form-group">
        <label for="carouselImg">輪播圖片</label>
        <img src="{{ asset('/images/carousel/'. $cdata->imgUrl) }}" id="nowimage" width="100%" />
        <input type="file" id="carouselImg" name="carouselImg" />
        <p class="help-block">限制 8 MB，建議解析度為 1280 × 620，若上傳非此比例之解析度圖片可能導致樣式跑位，此為必要項目。</p>
    </div>
    <div class="form-group text-center">
        <input type="submit" name="submit" value="送出" class="btn btn-success" />
        <a href="{{ route('admin.article.carousel', ['action'=> 'list']) }}" title="取消" class="btn btn-info">取消</a>
    </div>
</form>
@endsection