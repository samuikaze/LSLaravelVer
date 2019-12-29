@extends('frontend.layouts.master')

@section('title', '使用者操作')

@section('content')
@if(!Auth::check())
    <!-- 登入 / 註冊表單 -->
    <div class="memberForm">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <!-- Bootstrap 標籤頁 -->
                <ul class="nav nav-tabs">
                    @if($query['action'] == 'login' || $query['action'] == 'relogin') <li class="active"> @else <li> @endif<a href="#login-form" data-toggle="tab">登入</a></li>
                    {{-- 如果註冊功能有開啟才顯示 --}}
                    @if($query['action'] == 'register') <li class="active"> @else <li> @endif<a href="#register-form" data-toggle="tab">註冊</a></li>
                </ul>
                <div class="tab-content">
                    <div @if($query['action'] == 'login' || $query['action'] == 'relogin') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="login-form">
                        <form method="POST" action="{{ route('login') }}" style="margin-top: 1em;">
                            @csrf
                            <div class="form-group text-left">
                                <label for="username">使用者名稱</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="請輸入使用者名稱" value="{{ old('username') }}" required autocomplete="username" autofocus />
                            </div>
                            <div class="form-group text-left">
                                <label for="password">密碼</label>
                                <input type="password" class="form-control" name="password" placeholder="請輸入密碼" autocomplete="current-password" />
                            </div>
                            <input type="hidden" name="remember" checked />
                            <input type="hidden" name="refer" @if(url()->previous() != route('useraction')) value="{{ url()->previous() }}" @else value="{{ route('index') }}" @endif />
                            <input type="submit" class="btn btn-success btn-lg" name="submit" value="登入" />
                        </form>
                    </div>
                    {{-- 如果註冊功能有開啟才顯示 --}}
                    <div @if($query['action'] == 'register') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="register-form">
                        @if(Session::get('registerable') == 'on')
                            <form method="POST" action="{{ route('register') }}" style="margin-top: 1em;">
                                @csrf
                                <div class="form-group text-left">
                                    <label for="username">使用者名稱</label>
                                    <input type="text" class="form-control" name="username" id="username" placeholder="請輸入使用者名稱" />
                                </div>
                                <div class="form-group text-left">
                                    <label for="usernickname">暱稱</label>
                                    <input type="text" class="form-control" name="usernickname" placeholder="請輸入您的暱稱" />
                                </div>
                                <div class="form-group text-left">
                                    <label for="password">密碼</label>
                                    <input type="password" class="form-control" name="password" placeholder="請輸入密碼" />
                                </div>
                                <div class="form-group text-left">
                                    <label for="password_confirmation">確認密碼</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="請再次輸入密碼" />
                                </div>
                                <div class="form-group text-left">
                                    <label for="email">電子郵件</label>
                                    <input type="email" class="form-control" name="email" placeholder="請輸入電子信箱地址" />
                                </div>
                                <input type="hidden" name="refer" @if(url()->previous() != route('useraction')) value="{{ url()->previous() }}" @else value="{{ route('index') }}" @endif  />
                                <input type="submit" class="btn btn-success btn-lg" name="submit" value="註冊" />
                            </form>
                        @else
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">資訊</h3>
                                </div>
                                <div class="panel-body text-center">
                                    <h2 class="news-warn">註冊功能目前關閉</h2>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12 text-center">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">錯誤</h3>
                    </div>
                    <div class="panel-body">
                        <h2 class="news-warn">您已經登入了！<br /><br />
                            <div class="btn-group" role="group">
                                <?php echo "<a class=\"btn btn-lg btn-success\" onClick=\"javascript:history.back();\">返回上一頁</a>"; ?>
                                <a href="authentication.php?action=logout" class="btn btn-lg btn-danger">按此登出</a>
                            </div>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection