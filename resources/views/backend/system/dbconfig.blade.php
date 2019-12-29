@extends('backend.layouts.master')

@section('title', '資料庫管理 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">修復或最佳化網站資料庫</h3>
        </div>
        <div class="panel-body">
            網站使用久了會有資料分散問題，且若遇到斷電可能會有資料表損壞的問題，此時可以藉由下列選項最佳化或修復您的資料庫資料表。<br />
            若修復後資料庫能無法使用請聯絡本公司協助您排除問題。
            <div class="container-fluid text-center">
                <form method="POST" action="{{ route('admin.system.dboptimize', ['action'=> 'optimizedb']) }}" style="display: inline-block; width: auto!important;">
                    @csrf
                    <input type="submit" name="submit" value="最佳化資料表" class="btn btn-success" />
                </form>
                <form method="POST" action="{{ route('admin.system.dboptimize', ['action'=> 'repairdb']) }}" style="display: inline-block; width: auto!important;">
                    @csrf
                    <input type="submit" name="submit" value="修復資料表" class="btn btn-success" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection