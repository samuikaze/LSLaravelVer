@extends('backend.layouts.master')

@section('title', '編輯' . $privdata->privName . ' - 會員權限設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form action="{{ route('admin.member.doeditpriv', ['privid'=> $privdata->privNum]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="privnum">權限編號</label>
            <input type="text" name="privnum" class="form-control" id="privnum" value="{{ $privdata->privNum }}" placeholder="請輸入權限的編號，注意請輸入數字，已經存在的數字不可重複使用。">
        </div>
        <div class="form-group">
            <label for="privname">權限名稱</label>
            <input type="text" name="privname" class="form-control" id="privname" value="{{ $privdata->privName }}" placeholder="請輸入權限的名稱，已經存在的名稱不可重複使用。">
        </div>
        <div class="form-group text-center">
            <input type="submit" name="submit" value="送出" class="btn btn-success" />
            <a href="{{ route('admin.member.priv', ['action'=> 'list']) }}" class="btn btn-info">取消</a>
        </div>
    </form>
</div>
@endsection