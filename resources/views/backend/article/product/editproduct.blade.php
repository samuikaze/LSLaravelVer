@extends('backend.layouts.master')

@section('title', '編輯作品 - 作品設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form action="{{ route('admin.article.doeditproduct', ['pid'=> $pdata->prodOrder]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="prodname">作品名稱</label>
            <input type="text" name="prodname" id="prodname" class="form-control" value="{{ $pdata->prodTitle }}" placeholder="請輸入商品名稱，此為必填項" />
        </div>
        <div class="form-group">
            <label for="prodtype">作品類型</label>
            <input type="text" name="prodtype" id="prodtype" class="form-control" value="{{ $pdata->prodType }}" placeholder="請輸入作品的類型，此為必填項" />
        </div>
        <div class="form-group">
            <label for="prodplatform">作品平台</label>
            <input type="text" name="prodplatform" id="prodplatform" class="form-control" value="{{ $pdata->prodPlatform }}" placeholder="請輸入作品的執行平台，此為必填項" />
        </div>
        <div class="form-group">
            <label for="prodreldate">作品發售日期</label>
            <input type="datetime-local" name="prodreldate" id="prodreldate" class="form-control" value="{{ $reldate }}" placeholder="請輸入作品發售日期，未決定發售日期可留空" />
        </div>
        <div class="form-group">
            <label for="prodadddate">作品張貼日期</label>
            <p>{{ date('Y-m-d', strtotime($pdata->prodAddDate)) }}</p>
        </div>
        <script>
            $(function() {
                $("#prodreldate").datepicker({
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        showButtonPanel: true,
                        gotoCurrent: true
                    })
                    .datepicker(
                        "option", {
                            "dateFormat": "yy-mm-dd",
                            "showAnim": "fadeIn",
                        },
                        $.datepicker.regional["zh-TW"]
                    ).val("{{ date('Y-m-d', strtotime($pdata->prodRelDate)) }}");
            });
        </script>
        <div class="form-group">
            <label for="produrl">作品位址</label>
            <input type="text" name="produrl" id="produrl" class="form-control" value="{{ ($pdata->prodPageUrl == '#') ? '' : $pdata->prodPageUrl }}" placeholder="請輸入商品名稱，尚未架設網站可留空" />
        </div>
        <div class="form-group">
            <label for="proddescript">作品描述</label>
            <textarea id="editor1" name="proddescript" class="form-control noResize" rows="3">{!! $pdata->prodDescript !!}</textarea>
            <script>
                CKEDITOR.replace('editor1');
            </script>
        </div>
        <div class="form-group">
            <label for="prodimage">作品視覺圖</label>
            <input type="file" id="prodimage" name="prodimage" />
            <p class="help-block">檔案限制 8 MB，建議解析度為 586 × 670 或是等比例之解析度，若未上傳作品視覺圖則會使用系統預設視覺圖</p>
            @if ($pdata->prodImgUrl != "nowprint.jpg")
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="delprodimage" value="true" /> 刪除作品視覺圖
                    </label>
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="nowimage">目前作品視覺圖</label><br />
            @if (empty($pdata->prodImgUrl))
                <p class="form-control-static text-info" id="nowimage"><strong>此作品目前尚無視覺圖！</strong></p>
            @else
                <img src="{{ asset('images/products/' . $pdata->prodImgUrl) }}" id="nowimage" width="100%" />
            @endif
        </div>
        <div class="form-group text-center">
            <input type="submit" name="submit" value="送出" class="btn btn-success" />
            <a href="{{ route('admin.article.product', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
        </div>
    </form>
</div>
@endsection