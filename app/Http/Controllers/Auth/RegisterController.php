<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:50', 'unique:member'],
            'usernickname' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:1', 'confirmed'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:member,userEmail'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'userName' => $data['username'],
            'userNickname' => $data['usernickname'],
            'userPW' => Hash::make($data['password']),
            'userEmail' => $data['email'],
        ]);
    }

    /**
     * 執行註冊
     * @param Request $request
     * @return redirect()
     */
    public function register(Request $request)
    {
        // 驗證表單資料
        $validator = $this->validator($request->all());

        // 有問題就退回
        if ($validator->fails()) {
            $validator->errors()->add('type', 'error');
            return redirect(route('useraction') . '?a=register')
                   ->withErrors($validator)
                   ->withInput();
        }else{
            // 寫入資料庫
            $this->create($request->all());
            // 導回登入頁面
            return redirect(route('useraction'))
                   ->withErrors([
                       'msg' => '註冊成功，請重新登入',
                       'type' => 'info',
                   ]);
        }
    }
}
