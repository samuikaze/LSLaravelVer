<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // 如果不是 AJAX
        if (! $request->expectsJson()) {
            $request->session()->flash('errormsg', [
                'msg' => '該功能僅限會員使用，請先登入',
                'type' => 'error',
            ]);
            return route('useraction');
        }
        // AJAX 回應
        else{
            return response()->json(['error'=> '您未登入'], 401);
        }
    }
}
