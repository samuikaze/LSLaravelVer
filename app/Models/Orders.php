<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    // 變更預設的 ID 欄位名稱
    protected $primaryKey = 'orderID';
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
        'orderSerial', 'orderMember', 'orderContent', 'orderRealName', 'orderPhone', 'orderAddress', 'orderPrice', 'orderCasher', 'orderPattern', 'orderFreight', 'orderStatus'
    ];

    /**
     * 讓 Eloquent ORM 返回 datetime 格式
     */
    protected $dates = [
        'orderDate'
    ];

    /**
     * orderDetail 資料表關聯
     */
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class, 'orderID', 'orderID');
    }
}
