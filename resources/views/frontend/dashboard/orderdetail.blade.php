@extends('frontend.layouts.master')

@section('title', "訂單詳細資料 | 使用者設定")

@section('content')
<div class="row">
    <div class="col-md-10 col-md-push-1">
        <div class="col-sm-8">
            <div class="panel panel-info" style="margin-top: 1em;">
                <div class="panel-heading">
                    <h3 class="panel-title">訂購商品內容</h3>
                </div>
                <div class="panel-body">
                    @foreach($detaildata as $data)
                        @if($loop->index != 0)
                            <div class="clearfix"></div>
                            <hr class="divideTotal" />
                        @endif
                        {{-- 一個商品 --}}
                        <div class="form-group">
                            <div class="col-sm-8">
                                <img src="{{ asset("images/goods/".$data['image']) }}" alt="商品圖" class="img-responsive" />
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-5 control-label sessionCtrlTable">品名</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">{{ $data['name'] }}</p>
                                </div>
                                <label class="col-sm-5 control-label sessionCtrlTable">單價</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">{{ $data['price'] }} 元</p>
                                </div>
                                <label class="col-sm-5 control-label sessionCtrlTable">數量</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">{{ $data['qty'] }}</p>
                                </div>
                                <label class="col-sm-5 control-label sessionCtrlTable">小計</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">{{ $data['total'] }} 元</p>
                                </div>
                            </div>
                        </div>
                        {{-- /一個商品 --}}
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-info" style="margin-top: 1em;">
                <div class="panel-heading">
                    <h3 class="panel-title">訂單詳細資料</h3>
                </div>
                <div class="panel-body noPadding">
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">訂單編號</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['serial'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">訂貨人</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['realname'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">連絡電話</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['phone'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">付款方式</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['casher'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">取貨方式</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['pattern'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">送貨位置</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['address'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">運費</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['freight'] }} 元</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">應付金額</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['total'] }} 元</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">下訂日期</label>
                        <div class="col-sm-7">
                            <p class="form-control-static">{{ $orderdata['date'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label sessionCtrlTable">訂單狀態</label>
                        <div class="col-sm-7">
                            @if($orderdata['status'] == '已申請取消訂單') <p class="form-control-static" style="color: red;"><strong>{{ $orderdata['status'] }}</strong></p> @else <p class="form-control-static">{{ $orderdata['status'] }}</p> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 text-center" style="margin-bottom: 1em;">
            <a href="{{ route('dashboard.form', ['a'=> 'userorders']) }}" class="btn btn-lg btn-success">返回訂單管理</a>
        </div>
    </div>
</div>
@endsection