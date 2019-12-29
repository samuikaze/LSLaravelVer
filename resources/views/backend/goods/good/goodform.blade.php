@extends('backend.layouts.master')

@section('title', '商品設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 標籤 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a href="#goodlist" class="urlPush" data-url="list" aria-controls="goodlist" role="tab" data-toggle="tab">商品管理</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a href="#addgoods" class="urlPush" data-url="add" aria-controls="addgoods" role="tab" data-toggle="tab">上架商品</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="goodlist">
            {{-- 如果沒商品 --}}
            @if($info['nums'] == 0)
                <div class="panel panel-info" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">錯誤</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="news-warn">目前沒有上架任何商品！<br /><br />
                            <div class="btn-group" role="group">
                                <a class="btn btn-lg btn-info" href="?action=index">返回首頁</a>
                            </div>
                        </h2>
                    </div>
                </div>
            {{-- 如果有商品 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th class="goodsid">商品識別碼</th>
                            <th class="goodname">商品名稱</th>
                            <th class="goodother">商品價格</th>
                            <th class="goodother">商品在庫量</th>
                            <th class="goodadmin">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['data'] as $good)
                            <tr>
                                <td class="goodsid">{{ $good->goodsOrder }}</td>
                                <td class="goodname">{{ $good->goodsName }}</td>
                                <td class="goodother">{{ $good->goodsPrice }}</td>
                                <td class="goodother">{{ $good->goodsQty }}</td>
                                <td class="goodadmin">
                                    <a href="{{ route('admin.good.editgood', ['gid'=> $good->goodsOrder]) }}" class="btn btn-info">編輯</a>
                                    <a href="{{ route('admin.goods.delgoodconfirm', ['gid'=> $good->goodsOrder]) }}" class="btn btn-danger">移除</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
        </div>
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="addgoods">
            <form action="{{ route('admin.goods.addgood') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="goodname">商品名稱</label>
                    <input type="text" name="goodname" id="goodname" class="form-control" placeholder="請輸入商品名稱，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="goodprice">商品價格</label>
                    <input type="text" name="goodprice" id="goodprice" class="form-control" placeholder="請輸入商品價格，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="goodquantity">商品在庫量</label>
                    <input type="text" name="goodquantity" id="goodquantity" class="form-control" placeholder="請輸入商品在庫量，此為必填項" />
                </div>
                <div class="form-group">
                    <label for="gooddescript">商品描述</label>
                    <textarea id="editor1" name="gooddescript" class="form-control noResize" rows="3" placeholder="請輸入商品描述，此為必填項"></textarea>
                    <script>CKEDITOR.replace( 'editor1' );</script>
                </div>
                <div class="form-group">
                    <label for="goodstatus">商品販售狀態</label><br />
                    <label class="radio-inline">
                        <input type="radio" name="goodstatus" id="goodstatus" value="up" checked> 正常販售
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="goodstatus" id="goodstatus" value="down"> 暫停販售
                    </label>
                </div>
                <div class="form-group">
                    <label for="goodimage" id="prevImg">商品圖片</label>
                    <input type="file" id="goodimage" data-prevtype="add" name="goodimage" />
                    <p class="help-block">檔案限制 8 MB，建議解析度為 1000 × 501，若未上傳商品圖則會使用系統預設商品圖</p>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection