@extends('backend.layouts.master')

@section('title', '討論板設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 分頁 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a class="urlPush" data-url="list" href="#home" aria-controls="home" role="tab" data-toggle="tab">管理討論板</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a class="urlPush" data-url="add" href="#profile" aria-controls="profile" role="tab" data-toggle="tab">新建討論板</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <!-- 管理討論板 -->
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade in active" @else class="tab-pane fade"@endif id="home">
            {{-- 如果還沒新增討論板 --}}
            @if ($info['nums'] == 0) 
                <div class="panel panel-info" style="margin-top: 1em; padding-bottom: 8em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body">
                        <h2 class="info-warn">目前尚未新增任何討論板！</h2><br /><br />
                    </div>
                </div>
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th class="board-order">序</th>
                            <th class="board-title">討論板標題</th>
                            <th class="board-admin">討論板狀態</th>
                            <th class="board-admin">討論板管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['data'] as $board)
                            <tr>
                                <td class="board-order">{{ ( ($info['thisPage'] - 1) * $info['listnums']) + $loop->index + 1 }}</td>
                                <td class="board-title">{{ $board->boardName }}</td>
                                <td class="board-status">@if($board->boardHide == 1) 隱藏 @else 顯示 @endif</td>
                                <td class="board-admin">
                                    <a href="{{ route('admin.bbs.editboard', ['bid'=> $board->boardID]) }}" class="btn btn-info">編輯</a>
                                    <a href="{{ route('admin.bbs.delboardconfirm', ['bid'=> $board->boardID]) }}" class="btn btn-danger">刪除</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- 頁數按鈕 --}}
                @if($info['totalPage'] > 1)
                    <div class="text-center">
                        <ul class="pagination">
                            @if ($info['thisPage'] == 1) <li class="disabled"><a aria-label="Previous"> @else <li><a href="{{ route('admin.bbs.bbs', ['action'=> $info['action'], 'p' => $info['thisPage'] - 1]) }}" aria-label="Previous"> @endif <span aria-hidden="true">«</span></a></li>
                            @for($i = 1; $i <= $info['totalPage']; $i++)
                                @if ($info['thisPage'] == $i) <li class="active"><a href="{{ route('admin.bbs.bbs', ['action'=> $info['action'], 'p' => $i]) }}"> @else <li><a href="{{ route('admin.bbs.bbs', ['action'=> $info['action'], 'p' => $i]) }}"> @endif {{ $i }} @if ($info['thisPage'] == $i) <span class="sr-only">(current)</span> @endif </a></li>
                            @endfor
                            @if ($info['thisPage'] == $info['totalPage']) <li class="disabled"><a aria-label="Next"> @else <li><a href="{{ route('admin.bbs.bbs', ['action'=> $info['action'], 'p'=> $info['thisPage'] + 1]) }}" aria-label="Next"> @endif <span aria-hidden="true">»</span></a></li>
                        </ul>
                    </div>
                @endif
            @endif
        </div>
        <!-- 新建討論板 -->
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="profile">
            <form method="POST" action="{{ route('admin.bbs.createboard') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="boardname">討論板名稱</label>
                    <input type="text" class="form-control" name="boardname" id="boardname" placeholder="請輸入討論板名稱" />
                </div>
                <div class="form-group">
                    <label for="boarddescript">討論板描述</label>
                    <textarea name="boarddescript" class="form-control noResize" rows="3" placeholder="請輸入討論板描述"></textarea>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="hideboard" value="true" /> 隱藏討論板
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="boardimage" id="prevImg">討論板圖片</label>
                    <input type="file" id="boardimage" data-prevtype="add" name="boardimage" />
                    <p class="help-block">檔案大小限制 8 MB，建議解析度為 640 × 310</p>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection