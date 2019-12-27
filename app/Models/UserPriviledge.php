<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPriviledge extends Model
{
    protected $primaryKey = 'privNum';
    protected $table = 'mempriv';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'userPriviledge', 'privNum');
    }

    /**
     * 宣告使用 save() 和 create() 方法時可以寫入的欄位名稱
     *
     * @var array
     */
    protected $fillable = [
        'privNum', 'privName'
    ];
}
