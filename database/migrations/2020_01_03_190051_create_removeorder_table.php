<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemoveorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('removeorder', function (Blueprint $table) {
            $table->increments('reomveID')->unique()->comment('退訂申請識別碼');
            $table->integer('targetOrder')->comment('申請退訂的訂單系統編號');
            $table->string('removeReason', 100)->comment('申請理由');
            $table->timestamp('removeDate')->useCurrent()->comment('申請日期');
            $table->string('removeStatus', 20)->default('appling')->comment('申請狀態');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('removeorder');
    }
}
