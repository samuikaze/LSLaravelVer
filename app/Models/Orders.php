<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'userName', 'orderMember');
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'orderMember', 'orderContent', 'orderRealName', 'orderPhone', 'orderAddress', 'orderPrice', 'orderCasher', 'orderPattern', 'orderFreight', 'orderStatus'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'orderDate'
    ];
}
