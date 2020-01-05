<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBbspostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bbspost', function (Blueprint $table) {
            $table->increments('postID')->unique()->comment('貼文識別碼');
            $table->string('postTitle', 100)->comment('貼文標題');
            $table->string('postType', 50)->comment('貼文分類');
            $table->string('postContent', 5000)->comment('貼文內容');
            $table->string('postUserID', 100)->comment('貼文者');
            $table->timestamp('postTime')->useCurrent()->comment('貼文時間');
            $table->string('lastUpdateUserID', 50)->nullable()->comment('最後回文者');
            $table->timestamp('lastUpdateTime')->useCurrent()->comment('最後回文時間');
            $table->integer('postStatus')->default(0)->comment('貼文狀態');
            $table->timestamp('postEdittime')->nullable()->comment('文章編輯時間');
            $table->integer('postBoard')->comment('貼文所屬討論板');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bbspost');
    }
}
