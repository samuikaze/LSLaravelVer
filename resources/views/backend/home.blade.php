@extends('backend.layouts.master')

@section('title', '後台管理首頁')

@section('content')
    <div class="col-md-12">
        <div class="jumbotron">
            <h1>歡迎使用後台管理系統</h1>
            <hr />
            <p>請由上方導覽列開始設定網站！<br />您目前登入為：{{ Auth::user()->userNickname }}</p>
        </div>
    </div>
@endsection