<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone', 64)->comment('手机号');
            $table->string('type', 64)->comment('类型');
            $table->string('code', 16)->comment('验证码');
            $table->dateTime('expire_at')->comment('过期时间');
            $table->string('sender')->default('')->comment('发送人，如aliyun');
            $table->string('transaction_id', 128)->default('')->comment('发送人发回的唯一请求号');
            $table->dateTime('notify_at')->nullable()->comment('短信发送回执接收时间');
            $table->boolean('is_send_success')->default(true)->comment('是否发送成功，默认成功，除非回执通知发送失败');
            $table->boolean('is_used')->default(false)->comment('是否使用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_records');
    }
}
