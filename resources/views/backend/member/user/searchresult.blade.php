@extends('backend.layouts.master')

@section('title', '搜尋會員結果 - 會員帳號設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    {{-- 搜尋表單 --}}
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-info">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a style="text-decoration: none;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        顯示搜尋表單
                    </a>
                </h4>
            </div>
            <div id="collapseOne" @if($result['nums'] == 0) class="panel-collapse collapse in" @else class="panel-collapse collapse" @endif role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <form action="{{ route('admin.member.searchuser') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="form-group">
                            <label for="searchuser" class="col-sm-2 control-label">搜尋關鍵字</label>
                            <div class="col-sm-9">
                                <input type="text" name="searchuser" class="form-control" id="searchuser" placeholder="輸入搜尋關鍵字" value="{{ $inputdata['text'] }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="searchtarget" class="col-sm-2 control-label">搜尋目標</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="searchtarget">
                                    <option value="uid" @if($inputdata['type'] == 'uid') selected @endif>使用者 ID</option>
                                    <option value="username" @if($inputdata['type'] == 'username') selected @endif>使用者名稱</option>
                                    <option value="usernickname" @if($inputdata['type'] == 'usernickname') selected @endif>使用者暱稱</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            @if($result['nums'] == 0)
                                <div class="col-sm-offset-5 col-sm-7">
                                    <input type="submit" value="搜尋" class="btn btn-info">
                                    <a class="btn btn-info" href="{{ route('admin.member.user', ['action'=> 'list']) }}">返回會員一覽</a>
                                </div>
                            @else
                                <div class="col-sm-offset-6 col-sm-6">
                                    <input type="submit" value="搜尋" class="btn btn-info">
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- 如果搜不到結果 --}}
    @if($result['nums'] == 0)
        <div class="panel panel-info" style="margin-top: 1em; padding-bottom: 8em;">
            <div class="panel-heading">
                <h3 class="panel-title">資訊</h3>
            </div>
            <div class="panel-body">
                <h2 class="info-warn" style="padding-top: 4em;margin: 0vh auto;">找不到帳號，請重新執行一次搜尋！</h2><br /><br />
            </div>
        </div>
    @else
        <table class="table table-hover">
            <thead>
                <tr class="warning">
                    <th style="width: 10%;">會員編號</th>
                    <th style="width: 35%;">帳號</th>
                    <th style="width: 35%;">暱稱</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['data'] as $user)
                    <tr>
                        <td>{{ $user->uid }}</td>
                        <td>{{ $user->userName }}</td>
                        <td>{{ $user->userNickname }}</td>
                        <td>
                            @if($user->uid == 1)
                                <p>此為內建管理員帳號，不可編輯或刪除</p>
                            @else
                                <a href="{{ route('admin.member.edituser', ['uid'=> $user->uid]) }}" class="btn btn-info">管理</a>
                                <a href="{{ route('admin.member.deluserconfirm', ['uid'=> $user->uid]) }}" class="btn btn-danger">刪除</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="col-sm-offset-5 col-sm-7">
            <a class="btn btn-info" href="{{ route('admin.member.user', ['action'=> 'list']) }}">返回會員一覽</a>
        </div>
    @endif
</div>
@endsection