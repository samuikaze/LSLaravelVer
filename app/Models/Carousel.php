<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $primaryKey = 'imgID';
    protected $table = 'frontcarousel';
    public $timestamps = false;

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'imgUrl', 'imgDescript', 'imgReferUrl'
    ];
}
