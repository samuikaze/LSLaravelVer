<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('newsOrder')->unique()->comment('消息ID');
            $table->char('newsType', 5)->comment('消息類型');
            $table->string('newsTitle', 50)->comment('消息標題');
            $table->string('newsContent', 300)->comment('消息內文');
            $table->timestamp('postTime')->useCurrent()->comment('消息張貼時間');
            $table->integer('postUser')->comment('消息張貼者');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
