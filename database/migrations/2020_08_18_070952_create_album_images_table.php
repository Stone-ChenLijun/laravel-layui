<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('album_id')->default(0)->comment('相册id');
            $table->string('name', 1024)->default('')->comment('图片名称');
            $table->string('path', 1024)->default('')->comment('存储路径');
            $table->integer('size')->default(0)->comment('字节大小');
            $table->string('size_name')->default('')->comment('文件大小');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('album_images');
    }
}
