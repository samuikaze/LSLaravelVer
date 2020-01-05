<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('sID')->unique()->comment('系統ID');
            $table->string('userName', 50)->comment('使用者名稱');
            $table->string('sessionID', 60)->comment('階段ID');
            $table->string('useBrowser', 20)->default('未知')->comment('使用的瀏覽器');
            $table->string('ipRmtAddr', 39)->nullable()->comment('本次登入IP');
            $table->string('lastipRmtAddr', 39)->nullable()->comment('上次登入IP');
            $table->string('savedCart', 255)->nullable()->comment('儲存的購物車字串');
            $table->integer('savedTotal')->nullable()->comment('儲存的購物車總額');
            $table->datetime('loginTime')->useCurrent()->comment('本次登入時間');
            $table->datetime('lastLoginTime')->nullable()->comment('上次登入時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
