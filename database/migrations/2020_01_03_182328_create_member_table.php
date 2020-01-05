<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->increments('uid')->unique()->comment('會員ID');
            $table->string('userName', 50)->unique()->comment('使用者名稱');
            $table->string('userPW', 255)->comment('使用者密碼');
            $table->rememberToken()->comment('記住我欄位');
            $table->string('userNickname', 50)->comment('使用者暱稱');
            $table->string('userAvator', 30)->default('exampleAvator.jpg')->comment('使用者須擬形象');
            $table->string('userEmail', 50)->comment('使用者電子郵件地址');
            $table->timestamp('userRegDate')->useCurrent()->comment('使用者註冊日期');
            $table->integer('userPriviledge')->default(10)->comment('使用者權限');
            $table->string('userRealName', 50)->nullable()->comment('使用者真實姓名');
            $table->string('userPhone', 20)->nullable()->comment('使用者聯絡電話');
            $table->string('userAddress', 100)->nullable()->comment('取貨地址');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member');
    }
}
