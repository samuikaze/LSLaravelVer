<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbsboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bbsboard', function (Blueprint $table) {
            $table->increments('boardID')->unique()->comment('討論板ID');
            $table->string('boardName', 50)->comment('討論板名稱');
            $table->string('boardImage', 50)->default('default.jpg')->comment('討論板圖片');
            $table->string('boardDescript', 150)->comment('討論板描述');
            $table->timestamp('boardCTime')->useCurrent()->comment('討論板建立時間');
            $table->string('boardCreator', 100)->comment('討論板建立者');
            $table->tinyInteger('boardHide')->default(0)->comment('討論板是否隱藏');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bbsboard');
    }
}
