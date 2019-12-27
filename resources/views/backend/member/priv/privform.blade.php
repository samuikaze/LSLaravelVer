@extends('backend.layouts.master')

@section('title', '會員權限設定 | 後台管理首頁')

@section('content')
<div class="col-md-12">
    <!-- 分頁 -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" @if($info['action'] == 'list') class="active" @endif><a class="urlPush" data-url="list" href="#privlist" aria-controls="privlist" role="tab" data-toggle="tab">權限一覽</a></li>
        <li role="presentation" @if($info['action'] == 'add') class="active" @endif><a class="urlPush" data-url="add" href="#addpriv" aria-controls="addpriv" role="tab" data-toggle="tab">新增權限</a></li>
    </ul>
    <!-- 內容 -->
    <div class="tab-content">
        <div role="tabpanel" @if($info['action'] == 'list') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="privlist">
            {{-- 由於有內建的兩個權限，故不需要判斷是否有權限設定 --}}
            <table class="table table-hover">
                <thead>
                    <tr class="warning">
                        <th style="width: 10%;">權限編號</th>
                        <th style="width: 70%;">權限名稱</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($info['data'] as $priv)
                        <tr>
                            <td>{{ $priv->privNum }}</td>
                            <td>{{ $priv->privName }}</td>
                            <td>
                                @if($priv->privPreset == 1)
                                    <p>此為內建的權限設定，不可編輯或刪除</p>
                                @else
                                    <a href="{{ route('admin.member.editpriv', ['privid'=> $priv->privNum]) }}" class="btn btn-info">編輯</a>
                                    <a href="{{ route('admin.member.delprivconfirm', ['privid'=> $priv->privNum]) }}" class="btn btn-danger">刪除</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div role="tabpanel" @if($info['action'] == 'add') class="tab-pane fade active in" @else class="tab-pane fade" @endif id="addpriv">
            <form action="{{ route('admin.member.addpriv') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="privnum">權限編號</label>
                    <input type="text" name="privnum" class="form-control" id="privnum" placeholder="請輸入權限的編號，注意請輸入數字，已經存在的數字不可重複使用。">
                </div>
                <div class="form-group">
                    <label for="privname">權限名稱</label>
                    <input type="text" name="privname" class="form-control" id="privname" placeholder="請輸入權限的名稱，已經存在的名稱不可重複使用。">
                </div>
                <div class="form-group text-center">
                    <input type="submit" name="submit" value="送出" class="btn btn-success" />
                </div>
            </form>
        </div>
    </div>
</div>
@endsection