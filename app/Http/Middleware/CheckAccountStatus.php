<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckAccountStatus
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
        // 如果有登入且權限是停權
        if(Auth::check() && Auth::user()->userPriviledge == 3){
            // 不是 AJAX
            if (! $request->expectsJson()) {
                // 路由名稱不是 banned 或 logout 就通通導向到這頁
                if(!in_array(Route::currentRouteName(), ['banned', 'logout'])){
                    return redirect(route('banned'));
                }
            }
            // 是 AJAX
            else{
                if(!in_array(Route::currentRouteName(), ['banned', 'logout'])){
                    return response()->json(['error'=> '您的帳號已被停權！'], 403);
                }
            }
        }
        return $next($request);
    }
}
