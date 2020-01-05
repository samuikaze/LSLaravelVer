<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('notifyID')->unique()->comment('通知識別碼');
            $table->string('notifyContent', 300)->comment('通知內容');
            $table->string('notifyTitle', 100)->nullable()->comment('通知標題');
            $table->string('notifySource', 100)->comment('通知來源');
            $table->string('notifyTarget', 100)->comment('通知目標');
            $table->string('notifyURL', 200)->nullable()->comment('通知指向位址');
            $table->string('notifyStatus', 3)->default('u')->comment('通知狀態');
            $table->timestamp('notifyTime')->useCurrent()->comment('通知時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
