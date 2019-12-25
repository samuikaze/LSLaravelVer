@extends('backend.layouts.master')

@section('title', '輪播設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    {{-- 分頁項目 --}}
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a href="#carouseladmin" class="urlPush" data-url="list" aria-controls="carouseladmin" role="tab" data-toggle="tab">管理輪播</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a href="#carouseladd" class="urlPush" data-url="add" aria-controls="carouseladd" role="tab" data-toggle="tab">新增輪播</a></li>
    </ul>
    {{-- 分頁內容 --}}
    <div class="tab-content">
        <!-- 管理輪播 -->
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade"@endif id="carouseladmin">
            {{-- 如果還沒新增輪播圖 --}}
            @if ($info['nums'] == 0) 
                <div class="panel panel-info" style="margin-top: 1em; padding-bottom: 8em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body">
                        <h2 class="news-info">目前尚未新增任何輪播圖！</h2><br /><br />
                    </div>
                </div>
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th class="news-order">序</th>
                            <th class="news-title">輪播圖片</th>
                            <th class="news-admin">輪播管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 一個輪播項目 -->
                        @foreach($info['data'] as $cs)
                            <tr>
                                <td class="news-order">{{ $cs->imgID }}</td>
                                <td class="news-title">{{ $cs->imgUrl }}</td>
                                <td class="news-admin">
                                    <a href="{{ route('admin.article.editcarousel', ['cid'=> $cs->imgID]) }}" class="btn btn-info">管理</a>
                                    <a href="{{ route('admin.article.delcsconfirm', ['cid'=> $cs->imgID]) }}" class="btn btn-danger">刪除</a>
                                </td>
                            </tr>
                        @endforeach
                        <!-- /一個輪播項目 -->
                    </tbody>
                </table>
            @endif
        </div>
        <!-- 新增輪播 -->
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade"@endif id="carouseladd">
            <form action="{{ route('admin.article.addcarousel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="carouselDescript">輪播描述</label>
                    <input type="text" name="carouselDescript" id="carouselDescript" class="form-control" placeholder="請輸入顯示於輪播圖下方的描述文字，不填可留空" />
                </div>
                <div class="form-group">
                    <label for="carouselTarget">輪播位址</label>
                    <input type="text" name="carouselTarget" id="carouselTarget" class="form-control" placeholder="請輸入當按下輪播圖時欲跳轉的位址，不填可留空" />
                </div>
                <div class="form-group">
                    <label for="carouselImg" id="prevImg">輪播圖片</label>
                    <input type="file" id="carouselImg" data-prevtype="add" name="carouselImg" />
                    <p class="help-block">限制 8 MB，建議解析度為 1280 × 620，若上傳非此比例之解析度圖片可能導致樣式跑位，此為必要項目。</p>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
        @if($info['totalPage'] > 1)
        <!-- 頁數按鈕 -->
        <div class="text-center">
            <ul class="pagination">
                @if ($info['thisPage'] == 1) <li class="disabled"><a aria-label="Previous"> @else <li><a href="{{ route('admin.article.carousel', ['action'=> $info['action'], 'p' => $info['thisPage'] - 1]) }}" aria-label="Previous"> @endif <span aria-hidden="true">«</span></a></li>
                @for($i = 1; $i <= $info['totalPage']; $i++)
                    @if ($info['thisPage'] == $i) <li class="active"><a href="{{ route('admin.article.carousel', ['action'=> $info['action'], 'p' => $i]) }}"> @else <li><a href="{{ route('admin.article.carousel', ['action'=> $info['action'], 'p' => $i]) }}"> @endif {{ $i }} @if ($info['thisPage'] == $i) <span class="sr-only">(current)</span> @endif </a></li>
                @endfor
                @if ($info['thisPage'] == $info['totalPage']) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route('admin.article.carousel', ['action'=> $info['action'], 'p'=> $info['thisPage'] + 1]) }}" aria-label="Next"> @endif <span aria-hidden="true">»</span></a></li>
            </ul>
        </div>
        <!-- /頁數按鈕 -->
        @endif
    </div>
</div>
@endsection