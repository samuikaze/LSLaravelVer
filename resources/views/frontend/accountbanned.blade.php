@extends('frontend.layouts.master')

@section('title', '您的帳號已被停權')

@section('content')
<div class="panel panel-warning">
    <div class="panel-heading">
        <h3 class="panel-title">存取被拒</h3>
    </div>
    <div class="panel-body text-center">
        <h2 class="news-warn" style="color: #8a6d3b !important;">您的帳號已被停權！<br /><br />
            <div class="btn-group" role="group">
                <a href="{{ route('logout') }}" class="btn btn-lg btn-success">按此登出</a>
            </div>
        </h2>
    </div>
</div>
@endsection