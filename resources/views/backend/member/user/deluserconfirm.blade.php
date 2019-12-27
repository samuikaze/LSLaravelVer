@extends('backend.layouts.master')

@section('title', '確認刪除使用者帳號「' . $userdata->userName . '」 - 會員權限設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form onsubmit="return confirm('這將會刪除所有與這支帳號有關的資料，並且無法復原\n針對有問題的帳號建議使用「停權」權限來關閉帳號\n您仍確定要刪除嗎？');" class="form-horizontal" action="{{ route('admin.member.deleteuser', ['uid'=> $userdata->uid]) }}" method="POST">
        @csrf
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>確定要刪除 {{ $userdata->userName }} 這支帳號嗎？這會將所有與這支帳號相關的資料都一併刪除！</strong></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle" style="color: orange;"></i> 此功能會將所有與這支帳號相關的資料一併移除，建議使用「停權」權限來關閉有問題的帳號</div>
                <div class="form-group">
                    <label for="userregdate" class="col-sm-2 control-label">使用者虛擬形象</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><img src="{{ asset('images/userAvator/' . $userdata->userAvator) }}" width="15%" /></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userregdate" class="col-sm-2 control-label">帳號註冊日期</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $userdata->userRegDate }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userid" class="col-sm-2 control-label">使用者編號</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $userdata->uid }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="username" class="col-sm-2 control-label">使用者名稱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $userdata->userName }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userpriviledge" class="col-sm-2 control-label">使用者權限</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $priv }} ({{ $userdata->userPriviledge }})</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="usernickname" class="col-sm-2 control-label">使用者暱稱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $userdata->userNickname }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">電子郵件信箱</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $userdata->userEmail }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userrealname" class="col-sm-2 control-label">真實姓名</label>
                    <div class="col-sm-10">
                        @if(empty($userdata->userRealName))
                            <p class="form-control-static" style="color: gray;">未填寫</p>
                        @else
                            <p class="form-control-static">{{ $userdata->userRealName }}</p>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="userphone" class="col-sm-2 control-label">連絡電話</label>
                    <div class="col-sm-10">
                        @if(empty($userdata->userPhone))
                            <p class="form-control-static" style="color: gray;">未填寫</p>
                        @else
                            <p class="form-control-static">{{ $userdata->userPhone }}</p>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="useraddress" class="col-sm-2 control-label">連絡地址</label>
                    <div class="col-sm-10">
                        @if(empty($userdata->userAddress))
                            <p class="form-control-static" style="color: gray;">未填寫</p>
                        @else
                            <p class="form-control-static">{{ $userdata->userAddress }}</p>
                        @endif
                    </div>
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="刪除" class="btn btn-danger" />
                    <a href="{{ route('admin.member.user', ['action'=> 'list']) }}" class="btn btn-success">取消</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection