<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbsarticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bbsarticle', function (Blueprint $table) {
            $table->increments('articleID')->unique()->comment('回文識別碼');
            $table->string('articleTitle', 100)->nullable()->comment('回文標題');
            $table->string('articleContent', 5000)->comment('回文內容');
            $table->string('articleUserID', 100)->comment('回文者');
            $table->timestamp('articleTime')->useCurrent()->comment('回文時間');
            $table->integer('articleStatus')->default(0)->comment('回文狀態');
            $table->timestamp('articleEdittime')->nullable()->comment('回文編輯時間');
            $table->integer('articlePost')->comment('回文隸屬貼文');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bbsarticle');
    }
}
