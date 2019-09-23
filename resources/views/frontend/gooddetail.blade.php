@extends('frontend.layouts.master')

@if (!empty($goodData->goodsName))
    @section('title', "$goodData->goodsName | 商品詳細資料")
@else
    @section('title', "找不到商品 | 商品詳細資料")
@endif

@section('content')
    @if (empty($goodData->goodsName))
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">錯誤</h3>
            </div>
            <div class="panel-body text-center">
                <h2 class="news-warn">無法識別商品，請依正常程序檢視商品詳細資料。<br /><br />
                    <div class="btn-group" role="group">
                        <a class="btn btn-lg btn-info" href="{{ route('goods') }}">返回商品一覽</a>
                    </div>
                </h2>
            </div>
        </div>
    @else
        <div class="row goodDetail">
            <div class="col-md-4 goods-detail-img"><a data-fancybox href="{{ asset("images/goods/$goodData->goodsImgUrl")  }}"><img src="{{ asset("images/goods/$goodData->goodsImgUrl")  }}" class="img-responsive img-thumbnail"></a></div>
            <div class="col-md-7 thumbnail goods-detail-text">
                <h1 style="margin-bottom: 10px;">{{ $goodData->goodsName }}</h1><br />
                <div class="numbers">NT$ <span>{{ $goodData->goodsPrice }}</span></div>
                <div class="numbers">目前庫存：@if ($goodData->goodsQty <= $goodQtyDanger) <span style="font-weight: bold; color: #d9534f!important;">{{ $goodData->goodsQty }} &nbsp;&nbsp;數量不多，要買要快！ @else <span style="font-weight: bold; color: #5cb85c!important;">{{ $goodData->goodsQty }} @endif</span></div>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>注意！</strong> 賣家若要求您「使用LINE帳號私下聯絡或轉帳匯款」是常見的詐騙手法
                </div>
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    符合消費者保護法所定義企業經營者之賣家，應遵守消費者保護法第19條之規範。買家檢視商品時，應維持商品之原狀，以免影響退款權益。
                </div>
                <p class="goodDescript">{!! $goodData->goodsDescript !!}</p>
                <div class="col-md-6 col-xs-12">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td rowspan="4" class="warning" style="vertical-align: middle;"><i class="fas fa-cash-register"></i><br /> 接受付款方式</td>
                                <td><i class="far fa-credit-card"></i> 信用卡</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-money-bill-wave"></i> 超商取貨<br />（須事先付款）</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-money-bill-wave"></i> ATM 轉帳</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-money-bill-wave"></i> 超商取貨付款</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-xs-12">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td rowspan="4" class="success" style="vertical-align: middle;"><i class="fas fa-truck"></i><br /> 運送方式</td>
                                <td><i class="fas fa-store-alt"></i> 超商取貨付款</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-store-alt"></i> 超商取貨<br />（須事先付款）</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-shipping-fast"></i> 郵寄／貨運</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-inbox"></i> 郵局取貨</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
                <a id="goodsjCart{{ $goodData->goodsOrder }}" data-gid="{{ $goodData->goodsOrder }}" data-clicked="false" class="btn btn-info btn-lg btn-block<?php echo (empty($_SESSION['auth']))? "" : " joinCart"; ?>" <?php echo (empty($_SESSION['auth']))? "disabled=\"disabled\" title=\"此功能登入後才可使用\"" : "";?>>加入購物車</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center" style="margin-bottom: 1em;">
                <a href="{{ route('faq') }}" class="btn btn-default btn-lg">常見問題</a>
                <a href="javascript:history.back()" class="btn btn-info btn-lg">返回周邊商品一覽</a>
            </div>
        </div>
    @endif
@endsection