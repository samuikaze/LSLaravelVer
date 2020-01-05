<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderdetailtemp extends Model
{
    protected $table = 'orderdetailtemp';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'orderSerial', 'goodID', 'goodPrice', 'goodQty'
    ];
}
