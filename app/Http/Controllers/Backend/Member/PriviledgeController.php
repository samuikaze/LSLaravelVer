<?php

namespace App\Http\Controllers\Backend\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\User;
use App\Models\UserPriviledge;

class PriviledgeController extends Controller
{
    /**
     * 會員權限管理
     * @param Request $request Request 實例
     * @param string $action 目前顯示頁面
     * @return view 視圖
     */
    public function privindex(Request $request, $action)
    {
        // 把 action 放進 info 陣列內
        $info['action'] = $action;
        // 拿目前頁數
        if(!empty($request->query('p'))){
            $info['thisPage'] = $request->query('p');
        }else{
            $info['thisPage'] = 1;
        }
        // 從資料庫取得一頁要顯示的數量
        $info['listnums'] = (int)GlobalSettings::where('settingName', 'adminListNum')->value('settingValue');
        // 先處理總頁數
        $info['nums'] = UserPriviledge::count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得輪播項目
        $info['data'] = UserPriviledge::skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('privNum', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '會員權限新增與一覽'],
        ];
        return view('backend.member.priv.privform', compact('bc', 'info'));
    }

    /**
     * 編輯會員權限表單
     * @param Request $request Request 實例
     * @param int $privid 權限編號
     * @return view 視圖
     */
    public function editPriv(Request $request, $privid)
    {
        $privBuilder = UserPriviledge::where('privNum', $privid);
        // 如果找不到該會員權限
        if($privBuilder->count() == 0){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '沒有該會員權限！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建權限
        elseif($privBuilder->value('privPreset') == 1){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的會員權限不可編輯！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $privdata = $privBuilder->first();
        $bc = [
            ['url' => route('admin.member.priv', ['action'=> 'list']), 'name' => '會員權限新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['privid'=> $privid]), 'name' => '編輯' . $privdata->privName],
        ];
        return view('backend.member.priv.editpriv', compact('bc', 'privdata'));
    }

    /**
     * 刪除會員權限確認表單
     * @param Request $request Request 實例
     * @param int $privid 權限編號
     * @return view 視圖
     */
    public function delPrivConfirm(Request $request, $privid)
    {
        $privBuilder = UserPriviledge::where('privNum', $privid);
        // 如果找不到該會員權限
        if($privBuilder->count() == 0){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '沒有該會員權限！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建權限
        elseif($privBuilder->value('privPreset') == 1){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的會員權限不可刪除！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $privdata = $privBuilder->first();
        $bc = [
            ['url' => route('admin.member.priv', ['action'=> 'list']), 'name' => '會員權限新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['privid'=> $privid]), 'name' => '確認刪除' . $privdata->privName],
        ];
        return view('backend.member.priv.delprivconfirm', compact('bc', 'privdata'));
    }

    /**
     * 執行新增會員權限
     * @param Request $request Request 實例
     * @return redirect 重新導向實例
     */
    public function addPriv(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'privnum' => ['required', 'int', 'max:255', 'unique:mempriv,privNum'],
            'privname' => ['required', 'string', 'max:10', 'unique:mempriv,privName'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就寫入資料庫
        UserPriviledge::create([
            'privNum'=> $request->input('privnum'),
            'privName'=> $request->input('privname'),
        ]);
        return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
            'msg'=> '新增會員權限成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯會員權限
     * @param Request $request Request 實例
     * @param int $privid 權限編號
     * @return redirect 重新導向實例
     */
    public function fireEditPriv(Request $request, $privid)
    {
        $privBuilder = UserPriviledge::where('privNum', $privid);
        // 如果找不到該權限
        if($privBuilder->count() == 0){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '沒有該會員權限！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建權限
        elseif($privBuilder->value('privPreset') == 1){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的會員權限不可編輯！',
                'type'=> 'error',
            ]);
        }
        // 先處理要驗證的資料
        $thisPriv = $privBuilder->first();
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            // 不重複的檢查要忽略目前正在編輯的記錄
            'privnum' => ['required', 'int', 'max:255', Rule::unique('mempriv', 'privNum')->ignore($thisPriv->privNum, 'privNum')],
            'privname' => ['required', 'string', 'max:10', Rule::unique('mempriv', 'privName')->ignore($thisPriv->privNum, 'privNum')],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就更新資料庫
        $privBuilder->update([
            'privNum'=> $request->input('privnum'),
            'privName'=> $request->input('privname'),
        ]);
        return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
            'msg' => '編輯會員權限成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除會員權限
     * @param Request $request Request 實例
     * @param int $privid 權限編號
     * @return redirect 重新導向實例
     */
    public function fireDelPriv(Request $request, $privid)
    {
        $privBuilder = UserPriviledge::where('privNum', $privid);
        // 如果找不到該權限
        if($privBuilder->count() == 0){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '沒有該會員權限！',
                'type'=> 'error',
            ]);
        }
        // 如果是內建權限
        elseif($privBuilder->value('privPreset') == 1){
            return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
                'msg'=> '內建的會員權限不可編輯！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就先把有這個權限的帳號全改回一般會員權限
        User::where('userPriviledge', $privid)->update([
            'userPriviledge'=> 1,
        ]);
        // 然後再刪除這個會員權限
        $privBuilder->delete();
        return redirect(route('admin.member.priv', ['action'=> 'list']))->withErrors([
            'msg'=> '刪除會員權限成功，並修改擁有此權限之會員為一般會員',
            'type'=> 'success',
        ]);
    }
}
