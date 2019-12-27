@extends('backend.layouts.master')

@section('title', '管理使用者「' . $userdata->userName . '」 - 會員權限設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <div class="alert alert-warning" role="alert">此功能主要修改權限及不當資料用，其餘資料建議由會員自行管理</div>
    <form action="{{ route('admin.member.doedituser', ['uid'=> $userdata->uid]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="control-label">使用者名稱</label>
            <div class="col-sm-12">
                <p class="form-control-static">{{ $userdata->userName }}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">註冊日期</label>
            <div class="col-sm-12">
                <p class="form-control-static">{{ $userdata->userRegDate }}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="userpriviledge">使用者權限</label>
            <select class="form-control" name="userpriviledge" id="userpriviledge">
                @foreach($privs as $priv)
                    <option value="{{ $priv->privNum }}" @if($userdata->userPriviledge == $priv->privNum) selected @endif>{{ $priv->privName }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="usernickname">暱稱</label>
            <input type="text" name="usernickname" class="form-control" id="usernickname" value="{{ $userdata->userNickname }}" placeholder="請輸入暱稱">
        </div>
        <div class="form-group">
            <label for="password">密碼</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="請輸入密碼，如不修改請留空">
        </div>
        <div class="form-group">
            <label for="password_confirmation">確認密碼</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="請再次輸入密碼，如不修改請留空">
        </div>
        <div class="form-group">
            <label for="email">電子郵件</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ $userdata->userEmail }}" placeholder="請輸入電子信箱地址">
        </div>
        <div class="form-group">
            <label for="userrealname">真實姓名</label>
            <input type="text" name="userrealname" class="form-control" id="userrealname" placeholder="請輸入真實姓名，此項目用於訂購商品用" value="{{ $userdata->userRealName }}" />
        </div>
        <div class="form-group">
            <label for="userphone">電話</label>
            <input type="text" name="userphone" class="form-control" id="userphone" placeholder="請輸入連絡電話，此項目用於訂購商品用" value="{{ $userdata->userPhone }}" />
        </div>
        <div class="form-group">
            <label for="useraddress">地址</label>
            <input type="text" name="useraddress" class="form-control" id="useraddress" placeholder="請輸入地址，此項目用於訂購商品用" value="{{ $userdata->userAddress }}" />
        </div>
        <div class="form-group">
            <label for="avatorimage">虛擬形象</label>
            <div style="width: 100%; margin-bottom: 5px;"><img src={{ asset("images/userAvator/" . $userdata->userAvator) }} id="nowimage" width="15%" /></div>
            <input type="file" id="avatorimage" name="avatorimage" />
            <p class="help-block">接受格式為 JPG、PNG、GIF，大小最大接受到 8 MB，另虛擬形象只會顯示於討論區中</p>
        </div>
        {{-- 不是預設虛擬形象就提供刪除的功能 --}}
        @if ($userdata->userAvator != 'exampleAvator.jpg')
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="delavatorimage" value="true" /> 刪除虛擬形象
                    </label>
                </div>
            </div>
        @endif
        <div class="form-group text-center">
            <input type="submit" name="submit" value="送出" class="btn btn-success" />
            <a href="{{ route('admin.member.user', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
        </div>
    </form>
</div>
@endsection