@extends('backend.layouts.master')

@section('title', '訂單管理 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 標籤 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a href="#orderlist" class="urlPush" data-url="list" aria-controls="orderlist" role="tab" data-toggle="tab">進行中的訂單</a></li>
        <li role="presentation" @if($info['action'] == 'refund') class="active" @endif><a href="#refundlist" class="urlPush" data-url="refund" aria-controls="refundlist" role="tab" data-toggle="tab">申請退訂一覽</a></li>
        <li role="presentation" @if($info['action'] == 'finish') class="active" @endif><a href="#finishlist" class="urlPush" data-url="finish" aria-controls="finishlist" role="tab" data-toggle="tab">已完成的訂單</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <!-- 訂單一覽 -->
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="orderlist">
            {{-- 若沒有訂單 --}}
            @if($info['inprogress']['nums'] == 0)
                <div class="panel panel-warning" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="warning-warn">目前沒有任何進行中的訂單！<br /><br /></h2>
                    </div>
                </div>
            {{-- 有就顯示出來 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th>訂單編號</th>
                            <th>應付金額</th>
                            <th>訂單狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['inprogress']['data'] as $iporder)
                            <tr>
                                <td>{{ $iporder->orderID }}</td>
                                <td>{{ $iporder->orderPrice }}</td>
                                <td>{{ $iporder->orderStatus }}</td>
                                <td><a href="{{ route('admin.goods.orderdetail', ['oid'=> $iporder->orderID]) }}" class="btn btn-info">詳細</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- 退訂一覽 -->
        <div role="tabpanel" @if($info['action'] == 'refund') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="refundlist">
            {{-- 如果沒有已申請取貨或已取消的訂單 --}}
            @if($info['refund']['nums'] == 0)
                <div class="panel panel-warning" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="warning-warn">目前沒有任何申請取消或已取消的訂單！<br /><br /></h2>
                    </div>
                </div>
            {{-- 有就顯示出來 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th>訂單編號</th>
                            <th>應付金額</th>
                            <th>訂單狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['refund']['data'] as $rforder)
                            <tr>
                                <td>{{ $rforder->orderID }}</td>
                                <td>{{ $rforder->orderPrice }}</td>
                                @if($rforder->orderStatus == '已申請取消訂單')
                                    <td style="color: red;"><strong>{{ $rforder->orderStatus }}</strong></td>
                                @else
                                    <td>{{ $rforder->orderStatus }}</td>
                                @endif
                                <td><a href="{{ route('admin.goods.orderdetail', ['oid'=> $rforder->orderID]) }}" class="btn btn-info">詳細</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- 已結單一覽 -->
        <div role="tabpanel" @if($info['action'] == 'finish') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="finishlist">
            {{-- 若沒有已結單的訂單 --}}
            @if($info['finish']['nums'] == 0)
                <div class="panel panel-warning" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">資訊</h3>
                    </div>
                    <div class="panel-body text-center">
                        <h2 class="warning-warn">目前沒有任何已完成的訂單！<br /><br /></h2>
                    </div>
                </div>
            {{-- 有就顯示出來 --}}
            @else
                <table class="table table-hover">
                    <thead>
                        <tr class="warning">
                            <th>訂單編號</th>
                            <th>應付金額</th>
                            <th>訂單狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info['finish']['data'] as $fnorder)
                            <tr>
                                <td>{{ $fnorder->orderID }}</td>
                                <td>{{ $fnorder->orderPrice }}</td>
                                <td>{{ $fnorder->orderStatus }}</td>
                                <td><a href="{{ route('admin.goods.orderdetail', ['oid'=> $fnorder->orderID]) }}" class="btn btn-info">詳細</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection