<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 16)->default('')->comment('菜单名称');
            $table->string('match', 2048)->default('')->comment('下级路由匹配');
            $table->integer('parent_id')->default(0)->comment('上级菜单id');
            $table->string('url', 2048)->default('#')->comment('跳转地址');
            $table->integer('permission_id')->default(0)->comment('绑定权限id');
            $table->string('icon')->default('')->comment('lay-ui css 图标样式');
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
        Schema::dropIfExists('menus');
    }
}
