@extends('frontend.layouts.master')

@section('title', "使用者設定")

@section('content')
<div class="row">
    <div class="col-md-10 col-md-push-1">
    <?php /* if (!empty($_GET['msg']) && $_GET['msg'] == 'notifyerrnooid') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>無法識別訂單編號，請依正常程序操作！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'notifysuccess') { ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>已通知團隊您已付款！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removeerrnoremovereason') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>請確實輸入您的申請原因！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removeerrnoorderstatus') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>無法取得訂單狀態，請依正常程序操作！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removesuccess') { ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>完成取消訂單申請！</strong></h4>
        </div>
    <?php } */ ?>
        <!-- 標籤 -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" @if($board['display'] == 'userdata') class="active" @endif><a href="#usersetting" aria-controls="usersetting" role="tab" data-toggle="tab">資料管理</a></li>
            <li role="presentation" @if($board['display'] == 'userorders') class="active" @endif><a href="#orderlist" aria-controls="orderlist" role="tab" data-toggle="tab">訂單管理</a></li>
            <li role="presentation" @if($board['display'] == 'usersessions') class="active" @endif><a href="#sessioncontrol" aria-controls="sessioncontrol" role="tab" data-toggle="tab">登入管理</a></li>
        </ul>
        <!-- 內容 -->
        <div class="tab-content">
            <!-- 帳號資料管理畫面 -->
            <div role="tabpanel" @if($board['display'] == 'userdata') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="usersetting">
                <!-- 修改資料 -->
                <?php /* if (!empty($_GET['msg']) && $_GET['msg'] == 'usrseterremptypwdcnfrm') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>如欲修改密碼請確實填妥密碼與確認密碼欄位！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usersettingerrpwdcnfrm') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>密碼與確認密碼欄位輸入不一致，請再重新輸入一次！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usersettingerrfilesize') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>上傳的檔案過大，請重新上傳！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usersettingerrfiletype') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>上傳的檔案類型錯誤！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usrseterravatorupdel') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>修改語刪除虛擬形象無法同時進行，請確定是要刪除還是修改！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usrseterravatornodel') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>目前並沒有虛擬形象可以刪除！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'usersettingsuccess') { ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>資料修改成功！</strong></h4>
                    </div>
                <?php } */ ?>
                <form action="{{ route('dashboard.update.userdata') }}" method="POST" enctype="multipart/form-data" style="margin-top: 1em;">
                    @csrf
                    <div class="form-group">
                        <label for="username">使用者名稱</label>
                        <div class="col-sm-12">
                            <p class="form-control-static">{{ $userdata->userName }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="useremail">使用者權限</label>
                        <div class="col-sm-12">
                            <p class="form-control-static">{{ $userdata->userPriviledge }}</p>
                        </div>
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
                    <div class="form-group">
                        <label for="password">密碼</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="如不修改請留空" />
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">確認密碼</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="如不修改請留空" />
                    </div>
                    <div class="form-group">
                        <label for="usernickname">暱稱</label>
                        <input type="text" name="usernickname" class="form-control" id="usernickname" placeholder="請輸入欲修改的暱稱" value="{{ $userdata->userNickname }}" />
                    </div>
                    <div class="form-group">
                        <label for="email">電子郵件</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="請輸入欲修改的電子郵件" value="{{ $userdata->userEmail }}" />
                    </div>
                    <div class="form-group">
                        <label for="userrealname">真實姓名</label>
                        <input type="text" name="userrealname" class="form-control" id="userrealname" placeholder="請輸入您的真實姓名，此項目用於訂購商品用" value="{{ $userdata->userRealName }}" />
                    </div>
                    <div class="form-group">
                        <label for="userphone">電話</label>
                        <input type="text" name="userphone" class="form-control" id="userphone" placeholder="請輸入您的連絡電話，此項目用於訂購商品用" value="{{ $userdata->userPhone }}" />
                    </div>
                    <div class="form-group">
                        <label for="useraddress">地址</label>
                        <input type="text" name="useraddress" class="form-control" id="useraddress" placeholder="請輸入您的地址，此項目用於訂購商品用" value="{{ $userdata->userAddress }}" />
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="submit" value="確認修改" class="btn btn-success" />
                    </div>
                </form>
            </div>
            <!-- 訂單管理畫面 -->
            <div role="tabpanel" @if($board['display'] == 'userorders') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="orderlist">
                {{-- 若沒有下任何訂單 --}}
                @if($orderdata['nums'] == 0)
                    <div class="panel panel-info" style="margin-top: 1em;">
                        <div class="panel-heading">
                            <h3 class="panel-title">資訊</h3>
                        </div>
                        <div class="panel-body">
                            <h2 class="info-warn">目前沒有進行中的訂單！<br /><br />
                                <div class="btn-group" role="group">
                                    <a class="btn btn-lg btn-success" href="{{ route('goods') }}">前往選購</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                {{-- 有下訂過訂單 --}}
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr class="warning">
                                <th>訂單編號</th>
                                <th>應付金額</th>
                                <th>下訂日期</th>
                                <th class="fpattern-resp">取貨方式</th>
                                <th>訂單狀態</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 開始遞迴各訂單資料 --}}
                            @foreach($orderdata['data'] as $od)
                                <!-- 一個訂單項目 -->
                                <tr>
                                    <td>{{ $od->orderID }}</td>
                                    <td>{{ $od->orderPrice }}</td>
                                    <td>{{ $od->orderDate }}</td>
                                    <td class="fpattern-resp">{{ $od->orderPattern }}</td>
                                    @if($od->orderStatus == '已申請取消訂單') <td style="color: red;"><strong></strong> @else <td> @endif {{ $od->orderStatus }}</td>
                                    <td>
                                        <a href="{{-- 詳細資料網址 --}}" class="btn btn-info">詳細資料</a>
                                        @switch($od->orderStatus)
                                            @case('已出貨')
                                                <a href="actions.php?action=notifytaked&oid={{ $od->orderID }}" class="btn btn-info">通知已取貨</a>
                                                <a class="btn btn-danger" disabled="disabled">貨品已寄出</a>
                                                @break
                                            @case('已申請取消訂單')
                                                <a class="btn btn-info" disabled="disabled">審核中</a>
                                                <a class="btn btn-danger" disabled="disabled">申請審核中</a>
                                                @break
                                            @case('等待付款')
                                                <a href="actions.php?action=notifypaid&oid={{ $od->orderID }}" class="btn btn-info">通知已付款</a>
                                                @break
                                            @case('已通知付款')
                                                <a class="btn btn-info" disabled="disabled">已通知付款</a>
                                                <a href="?action=removeorder&oid={{ $od->orderID }}" class="btn btn-danger">取消訂單</a>
                                                @break
                                            @case('已取貨')
                                                <a href="?action=removeorder&oid={{ $od->orderID }}" class="btn btn-danger">申請退貨</a>
                                                @break
                                            @default
                                                <a href="?action=removeorder&oid={{ $od->orderID }}" class="btn btn-danger">取消訂單</a>
                                        @endswitch
                                    </td>
                                </tr>
                                <!-- /一個訂單項目 -->
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <!-- 登入階段管理畫面 -->
            <div role="tabpanel" @if($board['display'] == 'usersessions') class="tab-pane fade in active" @else class="tab-pane fade" @endif id="sessioncontrol">
                <?php /* if (!empty($_GET['msg']) && $_GET['msg'] == 'delsessionerrsid') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>未指定登入階段的識別碼，請依正常程序終止登入階段！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delsessionerrnodata') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>找不到該登入階段，請依正常程序終止登入階段！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delsessionerroperator') { ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>該登入階段身分與您登入之身分不符，請依正常程序終止登入階段！</strong></h4>
                    </div>
                <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'delsessionsuccess') { ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4><strong>該登入階段登出成功！</strong></h4>
                    </div>
                <?php } */ ?>
                <div class="panel panel-success" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">您目前的登入階段</h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label sessionCtrlTable">系統 ID</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $sessiondata['data'][$sessiondata['thisIndex']]->sID }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label sessionCtrlTable">使用瀏覽器</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $sessiondata['data'][$sessiondata['thisIndex']]->useBrowser }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label sessionCtrlTable">本次登入 IP</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $sessiondata['data'][$sessiondata['thisIndex']]->ipRmtAddr }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label sessionCtrlTable">上次登入 IP</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@if(empty($sessiondata['data'][$sessiondata['thisIndex']]->lastipRmtAddr)) <span style="color:darkgray;">沒有上次的登入記錄</span> @else {{ $sessiondata['data'][$sessiondata['thisIndex']]->lastipRmtAddr }} @endif</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label sessionCtrlTable">最後登入時間</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">{{ $sessiondata['data'][$sessiondata['thisIndex']]->loginTime }}</p>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <a href="{{ route('logout') }}" class="btn btn-danger btn-block">登出我的瀏覽階段</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- 如果還有其他的登入階段就顯示出來 --}}
                @if ($sessiondata['nums'] > 1)
                    <table class="table table-hover session-table">
                        <thead>
                            <tr class="warning">
                                <td class="ss-id ss-thead"><strong>系統 ID</strong></td>
                                <td class="ss-other ss-thead"><strong>登入 IP</strong></td>
                                <td class="ss-other ss-thead use-browser"><strong>使用瀏覽器</strong></td>
                                <td class="ss-other ss-thead"><strong>登入時間</strong></td>
                                <td class="ss-operate ss-thead"><strong>操作</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 遞迴各登入階段資料 --}}
                            @foreach ($sessiondata['data'] as $sd)
                                {{-- 如果 loop 的 index 是這的階段就跳過 --}}
                                @if ($loop->index == $sessiondata['thisIndex'])
                                    @continue;
                                @else
                                    <tr>
                                        <td class="ss-id">{{ $sd->sID }}</td>
                                        <td class="ss-other">{{ $sd->ipRmtAddr }}</td>
                                        <td class="ss-other use-browser">{{ $sd->useBrowser }}</td>
                                        <td class="ss-other">{{ $sd->loginTime }}</td>
                                        <td class="ss-operate"><a href="{{ route('dashboard.logout-session', ['sid'=> $sd->sID]) }}" class="btn btn-warning btn-block">登出此階段</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection