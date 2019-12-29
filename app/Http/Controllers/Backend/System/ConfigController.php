<?php

namespace App\Http\Controllers\Backend\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\UserPriviledge;

class ConfigController extends Controller
{
    /**
     * 主要系統設定
     * @param Request $request Request 實例
     * @return view 視圖
     */
    public function systemConfig(Request $request)
    {
        // 從資料庫取得系統設定
        $datas = GlobalSettings::orderBy('settingName', 'asc')->get();
        $info = [];
        // 把資料處理成陣列
        foreach($datas as $data){
            $info[$data->settingName] = $data->settingValue;
        }
        // 再從資料庫取權限資料
        $privs = UserPriviledge::orderBy('privNum', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName()), 'name' => '主要系統設定'],
        ];
        return view('backend.system.systemconfig', compact('bc', 'info', 'privs'));
    }

    /**
     * 資料庫管理
     * @param Request $request Request 實例
     * @return view 視圖
     */
    public function systemDB(Request $request)
    {
        $bc = [
            ['url' => route(Route::currentRouteName()), 'name' => '資料庫管理'],
        ];
        return view('backend.system.dbconfig', compact('bc'));
    }

    /**
     * 執行修改系統設定
     * @param Request $request Request 實例
     * @return Redirect 重新導向實例
     */
    public function fireModifyConfig(Request $request)
    {
        // 取得可設定的權限
        $datas = UserPriviledge::orderBy('privNum', 'asc')->get();
        $privs = [];
        foreach($datas as $data){
            array_push($privs, $data->privNum);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'numAdminList' => ['required', 'int'],
            'numNews' => ['required', 'int'],
            'numGoods' => ['required', 'int'],
            'numPosts' => ['required', 'int'],
            'numArticles' => ['required', 'int'],
            'numGoodQtyDanger' => ['required', 'int'],
            'adminPriv' => ['required', 'int', Rule::in($privs)],
            'backendPriv' => ['required', 'int', Rule::in($privs)],
            'registerable' => ['required', 'string', Rule::in(['on', 'off'])],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 沒問題就更新資料庫資料
        $configs = GlobalSettings::orderBy('settingName', 'asc')->get();
        $inputdata = [
            'adminListNum'=> $request->input('numAdminList'),
            'adminPriv'=> $request->input('adminPriv'),
            'articlesNum'=> $request->input('numArticles'),
            'backendPriv'=> $request->input('backendPriv'),
            'goodQtyDanger'=> $request->input('numGoodQtyDanger'),
            'goodsNum'=> $request->input('numGoods'),
            'newsNum'=> $request->input('numNews'),
            'postsNum'=> $request->input('numPosts'),
            'registerable'=> $request->input('registerable'),
        ];
        // 資料與原本不同再進行資料更新
        foreach($configs as $config){
            if($inputdata[$config->settingName] != $config->settingValue){
                GlobalSettings::where('settingName', $config->settingName)->update([
                    'settingValue'=> $inputdata[$config->settingName],
                ]);
            }
        }
        return redirect(route('admin.system.configs'))->withErrors([
            'msg'=> '更新系統設定成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行最佳化或修復資料庫
     * @param Request $request Request 實例
     * @param string $action 執行的行為
     * @return Redirect 重新導向實例
     */
    public function fireDBOptimize(Request $request, $action)
    {
        // 依照 $action 決定要執行的動作
        switch($action){
            case 'optimizedb':
                DB::select(DB::raw('OPTIMIZE TABLE `bbsarticle`, `bbsboard`, `bbspost`, `checkout`, `faqlist`, `frontcarousel`, `goodslist`, `member`, `mempriv`, `news`, `notifications`, `orderdetail`, `orders`, `productname`, `removeorder`, `sessions`, `systemsetting`;'));
                return redirect(route('admin.system.database'))->withErrors([
                    'msg'=> '最佳化資料表成功！',
                    'type'=> 'success',
                ]);
                break;
            case 'repairdb':
                DB::select(DB::raw('REPAIR TABLE `bbsarticle`, `bbsboard`, `bbspost`, `checkout`, `faqlist`, `frontcarousel`, `goodslist`, `member`, `mempriv`, `news`, `notifications`, `orderdetail`, `orders`, `productname`, `removeorder`, `sessions`, `systemsetting`;'));
                return redirect(route('admin.system.database'))->withErrors([
                    'msg'=> '修復資料表成功！',
                    'type'=> 'success',
                ]);
                break;
            default:
                return redirect(route('admin.system.database'))->withErrors([
                    'msg'=> '請依正常操作最佳化或修復資料庫！',
                    'type'=> 'error',
                ]);
        }
    }
}
