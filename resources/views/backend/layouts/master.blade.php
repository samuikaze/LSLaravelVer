<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <title>@yield('title') - 後台管理 | 洛嬉遊戲 L.S. Games</title>
    <meta charset="UTF-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="web-root" content="{{ URL::to('/') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" />
    <script type="text/javascript" src="{{ asset('js/jquery-2.2.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/i18n/datepicker-zh-TW.js')}}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/backend.js') }}"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/package.min.css') }}" type="text/css" />
    <link type="text/css" rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backend.css') }}" type="text/css" />
</head>
<body>
    <div class="container" style="width: 80%;">
        @include('backend.layouts.header')
        {{-- 麵包屑 --}}
        <div class="row content-body">
            <ol class="breadcrumb">
                @if (Route::currentRouteName() == 'admin.index')
                    <li class="active"><i class="fas fa-map-marker-alt"></i> 首頁</li>
                @else
                    <li><a href="{{ route('admin.index') }}"><i class="fas fa-map-marker-alt"></i> 首頁</a></li>
                    @foreach ($bc as $i => $b)
                        @if ($i == count($bc) - 1)
                            <li class="active">{{ $b['name'] }}</li>
                        @else
                            <li><a href="{{ $b['url'] }}">{{ $b['name'] }}</a></li>
                        @endif
                    @endforeach
                @endif
            </ol>
        </div>
        {{-- 顯示錯誤訊息（經由 withErrors 方法） --}}
        @if($errors->any())
            <div @if($errors->first('type') == 'error') class="alert alert-danger alert-dismissible fade in" @elseif($errors->first('type') == 'warning')  class="alert alert-warning alert-dismissible fade in"  @else class="alert alert-success alert-dismissible fade in" @endif role="alert" style="margin-top: 1em;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                @foreach($errors->all() as $msg)
                    @if(!$loop->last)
                        <h4><strong>{{ $msg }}</strong></h4>@if($loop->index != ($loop->count - 2)) <br /> @endif
                    @endif
                @endforeach
            </div>
        @endif
        {{-- 顯示錯誤訊息（經由 session 方法） --}}
        @if(! empty(session('errormsg')))
            <div @if(session('errormsg')['type'] == 'error') class="alert alert-danger alert-dismissible fade in" @elseif(session('errormsg')['type'] == 'warning')  class="alert alert-warning alert-dismissible fade in"  @else class="alert alert-success alert-dismissible fade in" @endif role="alert" style="margin-top: 1em;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4><strong>{{ session('errormsg')['msg'] }}</strong></h4>
            </div>
        @endif
        {{-- 顯示錯誤訊息（經由 ajax 方法） --}}
        <div id="ajaxmsg"></div>
        <div class="row">
            @yield('content')
        </div>
    </div>
</body>
</html>