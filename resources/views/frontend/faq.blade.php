@extends('frontend.layouts.master')

@section('title', '常見問題')

@section('content')
    <!-- FAQ 內容 -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題１</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題２</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題３</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題４</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題５</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題６</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">常見問題７</h3>
        </div>
        <div class="panel-body">解答內容。</div>
    </div>
    <div class="col-md-12 text-center">
        <a href="{{ route('contact') }}" class="btn btn-default btn-lg">找不到問題的解答嗎？</a>
    </div>
@endsection