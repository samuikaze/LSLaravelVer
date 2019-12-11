<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckLoginSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 驗證有沒有被登出（會員資料管理頁面可以管理登入）
        if(Auth::check())
        {
            // 取得帳號 ID
            $uid = Auth::user()->uid;
            // 取得 Session 中儲存的 session_id
            $sessId = $request->session()->get('loginSession');
            // 如果 Cookie 中的 SessionID 在 sessions 資料表中找不到就登出使用者
            if(User::find($uid)->sessions()->where('sessionID', $sessId)->count() == 0)
            {
                // 如果 Sessions 資料表中已經沒有這個帳號的資料就把登入資料整個登出清掉
                if(User::find($uid)->sessions()->count() == 0){
                    Auth::logout();
                }else{
                    // 否則就只登出這個裝置（token 不清）
                    $this->guard()->logoutCurrentDevice();
                }
                $request->session()->invalidate();
                return $next($request);
            }
        }
        return $next($request);
    }
}
