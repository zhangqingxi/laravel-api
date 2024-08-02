<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('admin')->create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id')->default(0)->comment('管理员ID');
            $table->string('url', 100)->default('')->comment('请求URL');
            $table->string('method', 10)->default('')->comment('请求方法');
            $table->string('ip', 30)->default('')->comment('客户端ip');
            $table->string('table_name', 100)->default('')->comment('表名');
            $table->unsignedInteger('table_id')->unsigned()->comment('表ID');
            $table->json('content')->comment('内容');
            $table->index('created_at');
            $table->timestamps();
            $table->comment('操作日志表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('operation_logs');
    }
};
