@extends('backend.layouts.master')

@section('title', '訂單' . $data['order']->orderID . '詳細資料 - 訂單管理 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    @if($data['order']->orderStatus == '已申請取消訂單')
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">申請取消訂單資料</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{ route('admin.goods.reviewrefund', ['oid'=> $data['order']->orderID]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-2 control-label">申請編號</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data['refund']->removeID }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">申請理由</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data['refund']->removeReason }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">申請日期</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data['refunddate'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">審核結果</label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <input type="radio" name="reviewResult" id="true" value="true"> 通過申請
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="reviewResult" id="false" value="false" checked> 駁回申請
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="reviewNotify">審核通知</label>
                        <div class="col-sm-10">
                            <textarea class="form-control noResize" name="reviewNotify" id="reviewNotify" rows="3" placeholder="請輸入審核理由，內容會顯示於用戶側的通知內，此欄位不可留空，並請控制於 150 字內。"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 text-center" style="margin-bottom: 1em;">
                        <input class="btn btn-danger" type="submit" name="submit" value="送出" />
                    </div>
                </form>
            </div>
        </div>
    @endif
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">訂單資料</h3>
        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <td>系統編號</td>
                        <td>{{ $data['order']->orderID }}</td>
                    </tr>
                    <tr>
                        <td>訂單編號</td>
                        <td>{{ $data['order']->orderSerial }}</td>
                    </tr>
                    <tr>
                        <td>訂貨人</td>
                        <td>{{ $data['user']->userNickname }} ({{ $data['order']->orderMember}})</td>
                    </tr>
                    <tr>
                        <td>聯絡電話</td>
                        <td>{{ $data['order']->orderPhone }}</td>
                    </tr>
                    <tr>
                        <td>付款方式</td>
                        @if(empty($data['order']->orderCasher))
                            <td style="color: gray;">此訂單使用貨到付款</td>
                        @else
                            <td>{{ $data['order']->orderCasher }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td>取貨方式</td>
                        <td>{{ $data['order']->orderPattern }}</td>
                    </tr>
                    <tr>
                        <td>送貨地點</td>
                        <td>{{ $data['order']->orderAddress }}</td>
                    </tr>
                    <tr>
                        <td>運費</td>
                        <td>{{ $data['order']->orderFreight }} 元</td>
                    </tr>
                    <tr>
                        <td>應付金額</td>
                        <td>{{ $data['order']->orderPrice }} 元</td>
                    </tr>
                    <tr>
                        <td>下訂日期</td>
                        <td>{{ $data['order']->orderDate }}</td>
                    </tr>
                    <tr>
                        <td>訂單狀態</td>
                        @if($data['order']->orderStatus == '已申請取消訂單')
                            <td class="text-danger"><strong>{{ $data['order']->orderStatus }}</strong></td>
                        @else
                            <td>{{ $data['order']->orderStatus }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr />
    <h3>訂單內容</h3>
    <table class="table table-hover">
        <thead>
            <tr class="info">
                <td>商品名稱</td>
                <td>下訂數量</td>
                <td>單價</td>
                <td>小計</td>
            </tr>
        </thead>
        <tbody>
            @foreach($data['goods'] as $good)
                <tr>
                    <td>{{ $good->goodsName }}</td>
                    <td>{{ $data['detail'][$loop->index]->goodQty }}</td>
                    <td>{{ $data['detail'][$loop->index]->goodPrice }} 元</td>
                    <td>{{ $data['detail'][$loop->index]->goodQty * $data['detail'][$loop->index]->goodPrice }} 元</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="form-group text-center">
        @if($data['order']->orderStatus == '等待付款')
            <a class="btn btn-success" disabled="disabled" title="待買家付款後才可行出貨">等待付款</a>
            <a href="{{ route('admin.goods.orders', ['action'=> 'list']) }}" class="btn btn-info">返回訂單一覽</a>
        @elseif (!in_array($data['order']->orderStatus, ['已出貨', '已申請取消訂單', '已結單']))
            <form method="POST" action="{{ route('admin.goods.modifyorder', ['oid'=> $data['order']->orderID]) }}">
                @csrf
                @switch($data['order']->orderStatus)
                    @case('已取貨')
                        <input type="hidden" name="action" value="finishorder" />
                        <input type="submit" class="btn btn-success" value="結單" />
                        @break
                    @default
                        <input type="hidden" name="action" value="notifyshipped" />
                        <input type="submit" class="btn btn-success" value="通知已出貨" />
                @endswitch
                <a href="{{ route('admin.goods.orders', ['action'=> 'list']) }}" class="btn btn-info">返回訂單一覽</a>
            </form>
        @else
            <a href="{{ route('admin.goods.orders', ['action'=> 'list']) }}" class="btn btn-info">返回訂單一覽</a>
        @endif
    </div>
</div>
@endsection