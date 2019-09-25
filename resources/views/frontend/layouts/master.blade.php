<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <title>@yield('title') | 洛嬉遊戲 L.S. Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="洛嬉遊戲 L.S. Games LSGames lsgames" />
    <link rel="shortcut icon" href="images/favicon.ico" />
    <script type="application/x-javascript">
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <script type="text/javascript" src="{{ asset('js/jquery-2.2.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/PreloadJS.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}" />
    <style type="text/css">
        /* Loading Screen */
        .loadscr {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100vw;
            height: 100vh;
            background-color: rgb(196, 134, 0);
            z-index: 999;
        }

        .loadscr .progress {
            width: 50%;
            margin: 15px auto;
        }

        .progress-bar {
            text-align: right !important;
        }

        .progress-bar > span {
            font-weight: bold;
            font-size: 1.2em;
            text-shadow: 0px 0px 5px #7fbbf1, 0px 0px 5px #7fbbf1, 0px 0px 5px #7fbbf1, 0px 0px 5px #7fbbf1, 0px 0px 5px #7fbbf1;
        }

        .loadscr .loadTitle {
            font-size: 3.5em;
            color: white;
            text-align: center;
            width: 100vw;
            padding-top: 30vh;
        }

        .loadscr .logo {
            width: 1.7em;
        }

        .loadscr .loadHint {
            width: 100%;
            text-align: center;
            font-size: 1.1em;
            color: white;
        }

        .pageWrap {
            display: none;
        }

        #progBar {
            width: 45%;
        }

        .sr-only {
            display: none;
        }

        @media(max-width:480px) {
            .loadscr .loadTitle {
                font-size: 3em;
                color: white;
                text-align: center;
                width: 100vw;
                padding-top: 30vh;
            }
            
            .loadscr .logo {
                width: 1.4em;
            }
            
            .loadscr .loadHint {
                width: 100%;
                text-align: center;
                font-size: 1em;
                color: white;
            }

            .loadscr .progress {
                width: 80%;
                margin: 15px auto;
            }
        }

        @media(max-width:568px) {
            .loadscr .loadTitle {
                font-size: 3em;
                color: white;
                text-align: center;
                width: 100vw;
                padding-top: 30vh;
            }
            
            .loadscr .logo {
                width: 1.4em;
            }
            
            .loadscr .loadHint {
                width: 100%;
                text-align: center;
                font-size: 1em;
                color: white;
            }

            .loadscr .progress {
                width: 90%;
                margin: 15px auto;
            }
        }

        @media(max-width:991px) {
            .loadscr .progress {
                width: 60%;
                margin: 15px auto;
            }
        }
    </style>
</head>
<body onload="loadProgress()">
    <div class="loadscr">
        <div class="loadTitle"><img src="{{ asset('images/logo.png') }}" class="logo" />&nbsp;&nbsp;&nbsp;L.S. Games</div>
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <span class="progressPercent"></span>
            </div>
        </div>
        <div class="loadHint">頁面載入中...</div>
    </div>
    <div class="pageWrap">
        <!-- loginform.php -->
        @include('frontend.layouts.header')
        <div class="about-section" id="about">
            <div id="content-wrap" class="container">
                <!-- 麵包屑 -->
                <ol class="breadcrumb">
                    @if (Route::currentRouteName() == 'index')
                        <li class="thisPosition" style="color: #23527c;"><i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;洛嬉遊戲</li>
                    @else
                        <li><a href="{{ route('index') }}"><i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;洛嬉遊戲</a></li>
                        @foreach ($bc as $i => $b)
                            @if ($i == count($bc) - 1)
                                <li class="thisPosition">{{ $b['name'] }}</li>
                            @else
                                <li><a href="{{ $b['url'] }}">{{ $b['name'] }}</a></li>
                            @endif
                        @endforeach
                    @endif
                    <!-- loginbutton.php -->
                </ol>
                @yield('content')
            </div>
        </div>
        @include('frontend.layouts.footer')
    </div>
</body>
</html>