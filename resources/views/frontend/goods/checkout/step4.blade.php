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
        <div class="col-md-12 cart-total">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">感謝您的訂購</h3>
                </div>
                <div class="panel-body">
                    您已順利完成訂購手續，訂單編號為 {{ $result['serial'] }}，應付金額（含運費）為 {{ $result['total'] }}</strong> 元。
                    <hr />
                    出貨須 3 ~ 5 個工作天，還請耐心等候，訂單的處理狀況也可以在會員選單中的「訂單確認」裡確認<br /><br />
                    <div class="alert alert-warning" role="alert"><strong>請注意</strong>&nbsp;&nbsp;取消訂單須經過審核後方可取消，已出貨之訂單則不可取消。</div>
                    <div class="form-group text-center" style="margin-top: 1em;">
                        <div class="btn-group btn-group-lg text-center" role="group">
                            <a href="{{ route('goods') }}" class="btn btn-info">返回商品頁面</a>
                            <a href="{{ route('dashboard.form', ['a'=> 'userorders']) }}" class="btn btn-success">確認訂單資料</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection