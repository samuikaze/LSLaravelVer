@extends('backend.layouts.master')

@section('title', '編輯' . $gooddata->goodsName . ' - 商品設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form action="{{ route('admin.good.doeditgood', ['gid'=> $gooddata->goodsOrder]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="goodname">商品名稱</label>
            <input type="text" name="goodname" id="goodname" class="form-control" placeholder="請輸入商品名稱，此為必填項" value="{{ $gooddata->goodsName }}" />
        </div>
        <div class="form-group">
            <label class="control-label" style="margin: 0;">商品上架時間</label>
            <div class="col-sm-12">
                <p class="form-control-static">{{ $uptime }}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" style="margin: 0;">商品上架者</label>
            <div class="col-sm-12">
                <p class="form-control-static">{{ $upuser }} ({{ $gooddata->goodsUp }})</p>
            </div>
        </div>
        <div class="form-group">
            <label for="goodstatus">商品販售狀態</label><br />
            <label class="radio-inline">
                <input type="radio" name="goodstatus" id="goodstatus" value="up" @if($gooddata->goodsStatus == 'up') checked @endif> 正常販售
            </label>
            <label class="radio-inline">
                <input type="radio" name="goodstatus" id="goodstatus" value="down" @if($gooddata->goodsStatus == 'down') checked @endif> 暫停販售
            </label>
        </div>
        <div class="form-group">
            <label for="goodprice">商品價格</label>
            <input type="number" name="goodprice" id="goodprice" class="form-control" placeholder="請輸入商品價格，此為必填項" value="{{ $gooddata->goodsPrice }}" />
        </div>
        <div class="form-group">
            <label for="goodquantity">商品在庫量</label>
            <input type="number" name="goodquantity" id="goodquantity" class="form-control" placeholder="請輸入商品在庫量，此為必填項" value="{{ $gooddata->goodsQty }}" />
        </div>
        <div class="form-group">
            <label for="gooddescript">商品描述</label>
            <textarea id="editor1" name="gooddescript" class="form-control noResize" rows="3" placeholder="請輸入商品描述，此為必填項">{!! $gooddata->goodsDescript !!}</textarea>
            <script>CKEDITOR.replace( 'editor1' );</script>
        </div>
        <div class="form-group">
            <label for="goodimage">商品圖片</label>
            <input type="file" id="goodimage" name="goodimage" />
            <p class="help-block">檔案限制 8 MB，建議解析度為 1000 × 501，若未上傳商品圖則會使用系統預設商品圖</p>
            {{-- 不是預設商品圖就提供刪除功能 --}}
            @if($gooddata->goodsImgUrl != "default.jpg")
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="delgoodimage" value="true" /> 刪除商品圖片
                    </label>
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="nowimage">目前商品圖片</label><br />
            @if(empty($gooddata->goodsImgUrl)) 
                <p class="form-control-static text-info" id="nowimage"><strong>此商品目前尚無圖片！</strong></p>
            @else
                <img src="{{ asset('images/goods/' . $gooddata->goodsImgUrl) }}" id="nowimage" width="100%" />
            @endif
        </div>
        <div class="form-group text-center">
            <input type="submit" name="submit" value="送出" class="btn btn-success" />
            <a href="{{ route('admin.goods.good', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
        </div>
    </form>
</div>
@endsection