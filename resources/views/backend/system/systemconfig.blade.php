@extends('backend.layouts.master')

@section('title', '主要系統設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <form class="form-horizontal" method="POST" action="{{ route('admin.system.modifyconfigs') }}">
        @csrf
        <div class="form-group">
            <label for="numAdminList" class="col-sm-2 control-label">後台管理顯示資料列數</label>
            <div class="col-sm-10">
                <input type="number" name="numAdminList" class="form-control" id="numAdminList" value="{{ $info['adminListNum'] }}" placeholder="後台顯示資料列數" />
            </div>
        </div>
        <div class="form-group">
            <label for="numNews" class="col-sm-2 control-label">最新消息單頁顯示行數</label>
            <div class="col-sm-10">
                <input type="number" name="numNews" class="form-control" id="numNews" value="{{ $info['newsNum'] }}" placeholder="最新消息單頁顯示行數" />
            </div>
        </div>
        <div class="form-group">
            <label for="numGoodQtyDanger" class="col-sm-2 control-label">週邊商品庫存紅字閥值</label>
            <div class="col-sm-10">
                <input type="number" name="numGoodQtyDanger" class="form-control" id="numGoodQtyDanger" value="{{ $info['goodQtyDanger'] }}" placeholder="週邊商品庫存紅字閥值" />
            </div>
        </div>
        <div class="form-group">
            <label for="numGoods" class="col-sm-2 control-label">週邊商品單頁顯示行數</label>
            <div class="col-sm-10">
                <input type="number" name="numGoods" class="form-control" id="numGoods" value="{{ $info['goodsNum'] }}" placeholder="週邊商品單頁顯示行數" />
            </div>
        </div>
        <div class="form-group">
            <label for="numPosts" class="col-sm-2 control-label">討論板單頁顯示項目數</label>
            <div class="col-sm-10">
                <input type="number" name="numPosts" class="form-control" id="numPosts" value="{{ $info['postsNum'] }}" placeholder="討論板單頁顯示項目數" />
            </div>
        </div>
        <div class="form-group">
            <label for="numArticles" class="col-sm-2 control-label">文章頁面單頁顯示個數</label>
            <div class="col-sm-10">
                <input type="number" name="numArticles" class="form-control" id="numArticles" value="{{ $info['articlesNum'] }}" placeholder="文章頁面單頁顯示個數" />
            </div>
        </div>
        <div class="form-group">
            <label for="adminPriv" class="col-sm-2 control-label">討論版管理權限授權</label>
            <div class="col-sm-10">
                <select id="adminPriv" name="adminPriv" class="form-control">
                    <option>-- 請選擇 --</option>
                    @foreach($privs as $priv)
                        <option value="{{ $priv->privNum }}" @if($info['adminPriv'] == $priv->privNum) selected @endif>{{ $priv->privName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="backendPriv" class="col-sm-2 control-label">後台登入權限授權</label>
            <div class="col-sm-10">
                <select id="backendPriv" name="backendPriv" class="form-control">
                    <option>-- 請選擇 --</option>
                    @foreach($privs as $priv)
                        <option value="{{ $priv->privNum }}" @if($info['backendPriv'] == $priv->privNum) selected @endif>{{ $priv->privName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">新帳號註冊</label>
            <div class="col-sm-10">
                <label class="radio-inline">
                    <input type="radio" name="registerable" id="true" value="on" @if($info['registerable'] == 'on') checked @endif> 開放註冊
                </label>
                <label class="radio-inline">
                    <input type="radio" name="registerable" id="false" value="off" @if($info['registerable'] == 'off') checked @endif> 關閉註冊
                </label>
            </div>
        </div>
        <div class="form-group text-center">
            <input type="submit" name="submit" value="送出" class="btn btn-success" />
        </div>
    </form>
</div>
@endsection