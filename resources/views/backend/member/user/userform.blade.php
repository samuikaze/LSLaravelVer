@extends('backend.layouts.master')

@section('title', '會員帳號設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 分頁 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a class="urlPush" data-url="list" href="#userlist" aria-controls="userlist" role="tab" data-toggle="tab">一般會員一覽</a></li>
        <li role="presentation" @if($info['action'] == 'blacklist') class="active" @endif><a class="urlPush" data-url="blacklist" href="#userblacklist" aria-controls="userblacklist" role="tab" data-toggle="tab">停權會員一覽</a></li>
        <li role="presentation" @if($info['action'] == 'search') class="active" @endif><a class="urlPush" data-url="search" href="#searchuser" aria-controls="searchuser" role="tab" data-toggle="tab">搜尋會員</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a class="urlPush" data-url="add" href="#adduser" aria-controls="adduser" role="tab" data-toggle="tab">新增會員</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        {{-- 未被停權會員一覽 --}}
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="userlist">
            {{-- 由於有內建管理員帳號，故不需要判斷是否有權限設定 --}}
            <table class="table table-hover">
                <thead>
                    <tr class="warning">
                        <th style="width: 10%;">會員編號</th>
                        <th style="width: 10%;">帳號</th>
                        <th style="width: 15%;">會員權限</th>
                        <th style="width: 45%;">暱稱</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($info['data'] as $user)
                        <tr>
                            <td>{{ $user->uid }}</td>
                            <td>{{ $user->userName }}</td>
                            <td>{{ $userpriv[$user->userPriviledge] }}</td>
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
        </div>
        {{-- 停權帳號一覽 --}}
        <div role="tabpanel" @if($info['action'] == 'blacklist') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="userblacklist">
            {{-- 如果沒有帳號被停權 --}}
            @if ($info['nums'] == 0) 
                <div class="panel panel-info" style="margin-top: 1em; padding-bottom: 8em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body">
                        <h2 class="info-warn" style="padding-top: 4em;margin: 0vh auto;">目前無使用者被停權</h2><br /><br />
                    </div>
                </div>
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th style="width: 10%;">會員編號</th>
                            <th style="width: 10%;">帳號</th>
                            <th style="width: 15%;">會員權限</th>
                            <th style="width: 45%;">暱稱</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['black'] as $user)
                            <tr>
                                <td>{{ $user->uid }}</td>
                                <td>{{ $user->userName }}</td>
                                <td>{{ $userpriv[$user->userPriviledge] }}</td>
                                <td>{{ $user->userNickname }}</td>
                                <td>
                                    <a href="{{ route('admin.member.edituser', ['uid'=> $user->uid]) }}" class="btn btn-info">管理</a>
                                    <a href="{{ route('admin.member.deluserconfirm', ['uid'=> $user->uid]) }}" class="btn btn-danger">刪除</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        {{-- 搜尋會員 --}}
        <div role="tabpanel" @if($info['action'] == 'search') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="searchuser">
            <form action="{{ route('admin.member.searchuser') }}" method="POST" class="form-horizontal">
                @csrf
                <div class="form-group">
                    <label for="searchuser" class="col-sm-2 control-label">搜尋關鍵字</label>
                    <div class="col-sm-9">
                        <input type="text" name="searchuser" class="form-control" id="searchuser" placeholder="輸入搜尋關鍵字">
                    </div>
                </div>
                <div class="form-group">
                    <label for="searchtarget" class="col-sm-2 control-label">搜尋目標</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="searchtarget">
                            <option value="" selected>-- 請選擇搜尋目標 --</option>
                            <option value="uid">使用者 ID</option>
                            <option value="username">使用者名稱</option>
                            <option value="usernickname">使用者暱稱</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-6 col-sm-6">
                        <input type="submit" value="搜尋" class="btn btn-info">
                    </div>
                </div>
            </form>
        </div>
        {{-- 新增帳號 --}}
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="adduser">
            <div class="alert alert-warning" role="alert">此功能建議於關閉註冊時才使用，註冊功能可於系統設定內關閉</div>
            <form action="{{ route('admin.member.adduser') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">使用者名稱</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="請輸入使用者名稱">
                </div>
                <div class="form-group">
                    <label for="userpriviledge">使用者權限</label>
                    <select class="form-control" name="userpriviledge" id="userpriviledge">
                        @foreach($info['priv'] as $priv)
                            <option value="{{ $priv->privNum }}" @if($priv->privName == '一般會員') selected @endif>{{ $priv->privName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="usernickname">暱稱</label>
                    <input type="text" name="usernickname" class="form-control" id="usernickname" placeholder="請輸入暱稱">
                </div>
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="請輸入密碼">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">確認密碼</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="請再次輸入密碼">
                </div>
                <div class="form-group">
                    <label for="email">電子郵件</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="請輸入電子信箱地址">
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection