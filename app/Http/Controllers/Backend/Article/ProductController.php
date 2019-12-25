<?php

namespace App\Http\Controllers\Backend\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * 顯示作品一覽與新增作品
     * @param Request $request Request 實例
     * @param string $action 目前顯示項目
     * @return view 視圖
     */
    public function productindex(Request $request, $action)
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
        $info['nums'] = Product::count();
        $info['totalPage'] = ceil($info['nums'] / $info['listnums']);
        // 從資料庫取得輪播項目
        $info['data'] = Product::skip(($info['thisPage'] - 1) * $info['listnums'])->take($info['listnums'])->orderBy('prodOrder', 'asc')->get();
        $bc = [
            ['url' => route(Route::currentRouteName(), ['action'=> $action]), 'name' => '作品新增與一覽'],
        ];
        return view('backend.article.product.productform', compact('bc', 'info'));
    }

    /**
     * 顯示編輯表單
     * @param Request $request Request 實例
     * @param int $pid 作品編號
     * @return view 視圖
     */
    public function editProduct(Request $request, $pid)
    {
        $pBuilder = Product::where('prodOrder', $pid);
        // 如果找不到該作品
        if($pBuilder->count() == 0){
            return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該作品！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $pdata = $pBuilder->first();
        $reldate = (empty($pdata->prodRelDate)) ? null : date('Y-m-d', strtotime($pdata->prodRelDate));
        $bc = [
            ['url' => route('admin.article.product', ['action'=> 'list']), 'name' => '作品新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['pid'=> $pid]), 'name' => '編輯作品'],
        ];
        return view('backend.article.product.editproduct', compact('bc', 'pdata', 'reldate'));
    }

    /**
     * 顯示刪除表單
     * @param Request $request Request 實例
     * @param int $pid 作品編號
     * @return view 視圖
     */
    public function delProdConfirm(Request $request, $pid)
    {
        $pBuilder = Product::where('prodOrder', $pid);
        // 如果找不到該作品
        if($pBuilder->count() == 0){
            return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該作品！',
                'type'=> 'error',
            ]);
        }
        // 沒錯就開始取資料
        $pdata = $pBuilder->first();
        $bc = [
            ['url' => route('admin.article.product', ['action'=> 'list']), 'name' => '作品新增與一覽'],
            ['url' => route(Route::currentRouteName(), ['pid'=> $pid]), 'name' => '刪除作品確認'],
        ];
        return view('backend.article.product.delprodconfirm', compact('bc', 'pdata'));
    }

    /**
     * 執行新增作品
     * @param Request $request Request 實例
     * @return Redirect 重新導向實例
     */
    public function addProduct(Request $request)
    {
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'prodname' => ['required', 'string', 'max:50'],
            'prodtype' => ['required', 'string', 'max:30'],
            'prodplatform' => ['required', 'string', 'max:50'],
            'prodreldate' => ['nullable', 'date_format:Y-m-d'],
            'produrl' => ['nullable', 'url', 'max:150'],
            'proddescript' => ['required', 'string', 'max:100'],
            'prodimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        // 如果有上傳檔案
        if($request->hasFile('prodimage')){
            $filename = 'prod-' . hexdec(uniqid()) . '.' . $request->file('prodimage')->extension();
            $request->file('prodimage')->storeAs('images/products/', $filename, 'htdocs');
        }
        // 沒有上傳檔案就設定為空字串
        else{
            $filename = 'nowprint.jpg';
        }
        // 寫入資料庫
        Product::create([
            'prodTitle'=> $request->input('prodname'),
            'prodImgUrl'=> $filename,
            'prodDescript'=> $request->input('proddescript'),
            'prodPageUrl'=> empty($request->input('produrl')) ? '#' : $request->input('produrl'),
            'prodType'=> $request->input('prodtype'),
            'prodPlatform'=> $request->input('prodplatform'),
            'prodRelDate'=> $request->input('prodreldate'),
        ]);
        return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
            'msg'=> '新增作品成功！',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行編輯作品
     * @param Request $request Request 實例
     * @param int $pid 作品編號
     * @return Redirect 重新導向實例
     */
    public function fireEditProduct(Request $request, $pid)
    {
        // 先檢查是不是有上傳檔案又把刪除檔案打勾
        if($request->hasFile('prodimage') && $request->input('delprodimage') == 'true'){
            return back()
                   ->withErrors([
                       'msg'=> '上傳與刪除作品視覺圖不能同時執行！',
                       'type'=> 'error',
                   ]);
        }
        $pBuilder = Product::where('prodOrder', $pid);
        // 如果找不到該作品
        if($pBuilder->count() == 0){
            return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該作品！',
                'type'=> 'error',
            ]);
        }
        // 驗證表單資料
        $validator = Validator::make($request->all(), [
            'prodname' => ['required', 'string', 'max:50'],
            'prodtype' => ['required', 'string', 'max:30'],
            'prodplatform' => ['required', 'string', 'max:50'],
            'prodreldate' => ['nullable', 'date_format:Y-m-d'],
            'produrl' => ['nullable', 'url', 'max:150'],
            'proddescript' => ['required', 'string', 'max:100'],
            'prodimage' => ['sometimes', 'file', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
            'delprodimage' => ['nullable', Rule::in(['true'])],
        ]);
        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        if($request->hasFile('prodimage')){
            // 如果本來就不是預設的作品視覺圖則需要先把檔案刪除
            if($pBuilder->value('prodImgUrl') != 'nowprint.jpg'){
                $oldFilename = 'images/products/'. $pBuilder->value('prodImgUrl');
                Storage::disk('htdocs')->delete($oldFilename);
            }
            // 決定新檔案名稱和副檔名
            $filename = 'prod-' . hexdec(uniqid()) . '.' . $request->file('prodimage')->extension();
            // 移動檔案
            $request->file('prodimage')->storeAs('images/products/', $filename, 'htdocs');
        }
        // 如果沒傳檔案但是要刪除檔案
        elseif($request->has('delprodimage') && $request->input('delprodimage') == 'true'){
            $oldFilename = 'images/products/'. $pBuilder->value('prodImgUrl');
            Storage::disk('htdocs')->delete($oldFilename);
            $filename = 'nowprint.jpg';
        }
        // 不做任何動作
        else{
            // 沒有傳檔案還是要從 Auth 裡取出檔案名稱，方便資料庫更新
            $filename = $pBuilder->value('prodImgUrl');
        }
        // 更新資料庫
        $pBuilder->update([
            'prodTitle'=> $request->input('prodname'),
            'prodImgUrl'=> $filename,
            'prodDescript'=> $request->input('proddescript'),
            'prodPageUrl'=> empty($request->input('produrl')) ? "#" : $request->input('produrl'),
            'prodType'=> $request->input('prodtype'),
            'prodPlatform'=> $request->input('prodplatform'),
            'prodRelDate'=> empty($request->input('prodreldate')) ? null : $request->input('prodreldate'),
        ]);
        return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
            'msg'=> '更新作品資料成功',
            'type'=> 'success',
        ]);
    }

    /**
     * 執行刪除作品
     * @param Request $request Request 實例
     * @param int $pid 作品編號
     * @return Redirect 重新導向實例
     */
    public function fireDelProduct(Request $request, $pid)
    {
        $pBuilder = Product::where('prodOrder', $pid);
        // 如果找不到該作品
        if($pBuilder->count() == 0){
            return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
                'msg'=> '找不到該作品！',
                'type'=> 'error',
            ]);
        }
        // 沒問題就先刪檔案
        if($pBuilder->value('prodImgUrl') != 'nowprint.jpg'){
            $filename = 'images/products/' . $pBuilder->value('prodImgUrl');
            Storage::disk('htdocs')->delete($filename);
        }
        // 刪除資料庫記錄
        $pBuilder->delete();
        return redirect(route('admin.article.product', ['action'=> 'list']))->withErrors([
            'msg'=> '刪除作品成功！',
            'type'=> 'success',
        ]);
    }
}
