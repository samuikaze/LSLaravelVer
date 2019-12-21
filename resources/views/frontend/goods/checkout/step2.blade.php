@extends('frontend.layouts.master')

@section('title', $checkoutinfo['title'] . ' | 結帳')

@section('content')
<div class="container">
    <div class="check">
        {{-- 結帳麵包屑 --}}
        <h1 class="orderBreadcrumb">
            @if($checkoutinfo['step'] == 1) <span> @endif<i class="fas fa-check-square"></i> 選擇付款及收貨方式 @if($checkoutinfo['step'] == 1) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 2) <span> @endif<i class="fas fa-scroll"></i> 輸入相關資料 @if($checkoutinfo['step'] == 2) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 3) <span> @endif<i class="fas fa-check-double"></i> 確認資料 @if($checkoutinfo['step'] == 3) </span> @endif &nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;&nbsp;
            @if($checkoutinfo['step'] == 4) <span> @endif<i class="fas fa-clipboard-check"></i> 完成訂單 @if($checkoutinfo['step'] == 4) </span> @endif
        </h1>
        <hr class="divideBC" />
        <div class="col-md-9 cart-total">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Step 2 - 輸入下訂資料</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('goods.checkout', ['step'=> 3]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="clientname">訂單姓名</label>
                            <input type="text" name="clientname" class="form-control" id="clientname" placeholder="請輸入您的姓名" @if(!empty($inputdata['name'])) value="{{ $inputdata['name'] }}" @endif required />
                        </div>
                        <div class="form-group">
                            <label for="clientphone">訂單連絡電話</label>
                            <input type="text" name="clientphone" class="form-control" id="clientphone" placeholder="請輸入您的電話" @if(!empty($inputdata['phone'])) value="{{ $inputdata['phone'] }}" @endif required />
                        </div>
                        <div class="form-group">
                            {{-- 如果需要真實地址 --}}
                            @if($checkoutinfo['isRAddr'] == 'true')
                                <label for="clientaddress">訂單收貨地址</label>
                                <input type="text" name="clientaddress" class="form-control" id="clientaddress" placeholder="請輸入您的收貨地址" @if(!empty($inputdata['address'])) value="{{ $inputdata['address'] }}" @endif required />
                            {{-- 如果是需要郵局或超商名稱 --}}
                            @else
                                <label for="clientaddress">郵局或超商名稱</label>
                                <input type="text" name="clientaddress" class="form-control" id="clientaddress" placeholder="請輸入最近的郵局或超商名稱" @if(!empty($inputdata['address'])) value="{{ $inputdata['address'] }}" @endif required />
                            @endif
                        </div>
                        @if($checkoutinfo['cashtype'] == 'cash')
                            <div class="form-group">
                                <label for="clientcasher">付款方式</label>
                                <select class="form-control" name="clientcasher" id="clientcasher">
                                    <option value="">請選擇付款方式</option>
                                    @foreach($checkoutinfo['casher'] as $cs)
                                        <option value="{{ $cs }}" @if($inputdata['casher'] == $cs) selected @endif>{{ $cs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="fPattern">結帳方式</label>
                            <div class="col-sm-12">
                                <p class="form-control-static">{{ $checkoutinfo['pattern'] }}</p>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <a href="{{ route('goods.checkout', ['step'=> 1]) }}" class="btn btn-info btn-lg" title="回上一步修改結帳方式">上一步</a>
                            <input type="submit" name="submit" class="btn btn-success btn-lg" value="下一步" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- 側邊欄 --}}
        <div class="col-md-3 cart-total">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">總額</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="totPanel"><span class="cartPanelSmall">小計</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ <span id="ajaxTotal">{{ $cart['total'] }}</span></span></div>
                        <div class="totPanel"><span class="cartPanelSmall">運費</span></div>
                        <div class="totValPanel"><span class="cartPanelSmall">NT$ {{ $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                        <hr class="divideTotal" />
                        <div class="totPanel"><span class="cartPanel">總計</span></div>
                        <div class="totValPanel"><span class="cartPanel">NT$ {{ $cart['total'] + $checkoutinfo['fee'] }}</span></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection