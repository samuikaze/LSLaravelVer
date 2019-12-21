<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderdetail';
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Orders::class, 'orderID', 'orderID');
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'orderID', 'goodID', 'goodQty', 'goodPrice'
    ];
}
