@extends('backend.layouts.master')

@section('title', '作品設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 分頁 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a href="#productlist" class="urlPush" data-url="list" aria-controls="productlist" role="tab" data-toggle="tab">管理作品</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a href="#addproduct" class="urlPush" data-url="add" aria-controls="addproduct" role="tab" data-toggle="tab">新增作品</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="productlist">
            {{-- 若目前無作品 --}}
            @if ($info['nums'] == 0)
                <div class="panel panel-info" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">訊息</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="info-warn">目前沒有任何作品！</h2>
                    </div>
                </div>
            {{-- 有作品就顯示 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th class="news-order">作品編號</th>
                            <th class="news-title">作品名稱</th>
                            <th class="news-admin">管理操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['data'] as $pd)
                            <tr>
                                <td class="news-order">{{ $pd->prodOrder }}</td>
                                <td class="news-title">{{ $pd->prodTitle }}</td>
                                <td class="news-admin">
                                    <a href="{{ route('admin.article.editproduct', ['pid'=> $pd->prodOrder]) }}" class="btn btn-info">編輯</a>
                                    <a href="{{ route('admin.article.delprodconfirm', ['pid'=> $pd->prodOrder]) }}" class="btn btn-danger">刪除</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="addproduct">
            <form action="{{ route('admin.article.addproduct') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="prodname">作品名稱</label>
                    <input type="text" name="prodname" id="prodname" class="form-control" placeholder="請輸入作品名稱，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="prodtype">作品類型</label>
                    <input type="text" name="prodtype" id="prodtype" class="form-control" placeholder="請輸入作品的類型，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="prodplatform">作品平台</label>
                    <input type="text" name="prodplatform" id="prodplatform" class="form-control" placeholder="請輸入作品的執行平台，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="prodreldate">作品發售日期</label>
                    <input type="datetime-local" name="prodreldate" id="prodreldate" class="form-control" placeholder="請輸入作品發售日期，發售日期若未定則請留空" />
                </div>
                <script>
                    $(function() {
                        $("#prodreldate").datepicker().datepicker("option", {
                            "dateFormat": "yy-mm-dd",
                            "showAnim": "fadeIn"
                        });
                    });
                </script>
                <div class="form-group">
                    <label for="produrl">作品位址</label>
                    <input type="text" name="produrl" id="produrl" class="form-control" placeholder="請輸入作品位址，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="proddescript">作品描述</label>
                    <textarea id="editor1" name="proddescript" class="form-control noResize" rows="3"></textarea>
                    <script>
                        CKEDITOR.replace('editor1');
                    </script>
                </div>
                <div class="form-group">
                    <label for="prodimage" id="prevImg">作品視覺圖</label>
                    <input type="file" id="prodimage" data-prevtype="add" name="prodimage" />
                    <p class="help-block">檔案限制 8 MB，建議解析度為 586 × 670 或是等比例之解析度，若未上傳作品視覺圖則會使用系統預設視覺圖</p>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection