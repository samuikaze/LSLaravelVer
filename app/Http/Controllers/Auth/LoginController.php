<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Sessions;
use Browser;
use Carbon\Carbon;
use Cookie;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 變更登入使用者名稱使用的欄位
     */
    public function username()
    {
        return 'userName';
    }

    /**
     * 顯示登入頁面
     */
    public function showForm(Request $request)
    {
        // 從網址取得目前是登入還是註冊
        if(!empty($request->query('a'))){
            $query['action'] = $request->query('a');
        }else{
            $query['action'] = 'login';
        }
        $bc = [
            ['url' => route('useraction'), 'name' => '使用者操作']
        ];
        return view('frontend.useraction', compact('bc', 'query'));
    }

    /**
     * 執行登入
     */
    public function login(Request $request)
    {
        // 取得表單所有內容
        $input = $request->all();
        // 驗證輸入的表單內容
        $validator = Validator::make($input,[
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string']
        ]);

        // 若驗證失敗
        if ($validator->fails()) {
            // 針對錯誤訊息新增一欄訊息類別
            $validator->errors()->add('type', 'error');
            return back()
                   ->withErrors($validator)
                   ->withInput();
        }
        $loginUser = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];
        // 驗證使用者資訊
        if (Auth::attempt($loginUser, true)) {
            // 寫入登入階段的資料表
            // Session::setId(Sess_Id)
            $session_id = session()->getId();
            Sessions::create([
                'userName' => $request->input('username'),
                'sessionID' => $session_id,
                'useBrowser' => Browser::browserFamily(),
                'ipRmtAddr' => $request->ip(),
                'loginTime' => Carbon::now()->timestamp,
            ]);
            // 通過驗證就導向至欲導向的頁面
            $redirectTarget = $request->input('refer');
            // 把 refer 網址丟進 session 的 flash 內
            $request->session()->flash('refer', $redirectTarget);
            // 把 SESSION_ID 寫入 Cookie 中
            $cookie = cookie('loginSession', $session_id, 2629800);
            $request->session()->put('sessionStillAlive', 'true');
            return redirect($redirectTarget)->withCookie($cookie);
        }
        // 驗證失敗
        else
        {
            return redirect(route('useraction'))
                   ->withInput()
                   ->withErrors([
                       'msg' => '找不到使用者名稱或密碼不正確',
                       'type' => 'error',
                    ]);
        }
    }

    /**
     * 執行登出
     */
    public function logout(Request $request)
    {
        // 取得帳號 ID
        $uid = Auth::user()->uid;
        // 取得 Cookie 中儲存的 session_id
        $sessId = Cookie::get('loginSession');
        // 刪除 Sessions 資料表中的資料
        User::find($uid)->sessions()->where('sessionID', $sessId)->delete();
        // 如果 Sessions 資料表中已經沒有這個帳號的資料就把登入資料整個登出清掉
        if(User::find($uid)->sessions()->count() == 0){
            // 用此方式 remember_token 會重新產生
            Auth::logout();
        }else{
            // 否則就只登出這個裝置（token 不蟲新產生）
            Auth::logoutCurrentDevice();
        }
        $request->session()->invalidate();
        return back()->withCookie(cookie()->forget('loginSession'));
    }
}
