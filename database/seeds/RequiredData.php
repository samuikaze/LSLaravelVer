<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\CheckoutPattern;
use App\Models\GlobalSettings;
use App\Models\User;
use App\Models\UserPriviledge;

class RequiredData extends Seeder
{
    /**
     * 將必要的資料先倒進資料庫內
     *
     * @return void
     */
    public function run()
    {
        // 結帳方式
        $checkouts = [
            ['pattern'=> '超商取貨付款', 'fee'=> 60, 'type'=> 'freight', 'cashType'=> 'nocash', 'isRAddr'=> 'false'],
            ['pattern'=> '超商取貨', 'fee'=> 60, 'type'=> 'freight', 'cashType'=> 'cash', 'isRAddr'=> 'false'],
            ['pattern'=> '郵局取貨', 'fee'=> 70, 'type'=> 'freight', 'cashType'=> 'cash', 'isRAddr'=> 'false'],
            ['pattern'=> '貨送到府', 'fee'=> 70, 'type'=> 'freight', 'cashType'=> 'cash', 'isRAddr'=> 'true'],
            ['pattern'=> '信用卡', 'fee'=> 0, 'type'=> 'casher', 'cashType'=> null, 'isRAddr'=> null],
            ['pattern'=> 'ATM 轉帳', 'fee'=> 0, 'type'=> 'casher', 'cashType'=> null, 'isRAddr'=> null],
            ['pattern'=> '超商代碼繳費', 'fee'=> 0, 'type'=> 'casher', 'cashType'=> null, 'isRAddr'=> null],
            ['pattern'=> '郵局無摺存款', 'fee'=> 0, 'type'=> 'casher', 'cashType'=> null, 'isRAddr'=> null],
        ];
        CheckoutPattern::insert($checkouts);
        
        // 最高管理員帳號資料
        User::insert([
            'userName'=> 'admin',
            'userPW'=> Hash::make('123'),
            'userNickname'=> '超級管理員',
            'userEmail'=> 'example@example.com',
            'userPriviledge'=> 99,
        ]);

        // 權限資料
        $privs = [
            ['privNum'=> 1, 'privName'=> '禁止發言', 'privPreset'=> 1],
            ['privNum'=> 2, 'privName'=> '禁止下訂', 'privPreset'=> 1],
            ['privNum'=> 3, 'privName'=> '停權', 'privPreset'=> 1],
            ['privNum'=> 10, 'privName'=> '一般使用者', 'privPreset'=> 1],
            ['privNum'=> 99, 'privName'=> '超級管理員', 'privPreset'=> 1],
        ];
        UserPriviledge::insert($privs);

        // 系統設定值
        $settings = [
            ['settingName'=> 'adminListNum', 'settingValue'=> 15],
            ['settingName'=> 'adminPriv', 'settingValue'=> 99],
            ['settingName'=> 'articlesNum', 'settingValue'=> 10],
            ['settingName'=> 'backendPriv', 'settingValue'=> 99],
            ['settingName'=> 'goodQtyDanger', 'settingValue'=> 15],
            ['settingName'=> 'goodsNum', 'settingValue'=> 9],
            ['settingName'=> 'newsNum', 'settingValue'=> 6],
            ['settingName'=> 'postsNum', 'settingValue'=> 10],
            ['settingName'=> 'registerable', 'settingValue'=> 'on'],
        ];
        GlobalSettings::insert($settings);
    }
}
