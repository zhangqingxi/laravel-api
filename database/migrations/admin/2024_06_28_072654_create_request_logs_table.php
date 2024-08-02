<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateRequestLogsTable extends Migration
{
    public function up()
    {
        Schema::connection('admin')->create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 150)->unique()->comment('请求ID');
            $table->string('host', 50)->default('')->comment('请求主机');
            $table->string('url', 100)->default('')->comment('请求URL');
            $table->string('method', 10)->default('')->comment('请求方法');
            $table->string('ip', 45)->default('')->comment('请求IP');
            $table->json('headers')->nullable()->comment('请求头');
            $table->json('request_data')->nullable()->comment('请求数据');
            $table->text('encrypt_request_data')->nullable()->comment('加密的请求数据');
            $table->json('response_data')->nullable()->comment('响应数据');
            $table->text('encrypt_response_data')->nullable()->comment('加密的响应数据');
            $table->json('exception_data')->nullable()->comment('异常响应数据');
            $table->unsignedTinyInteger('http_status')->default(200)->comment('HTTP状态码');
            $table->timestamps();
            $table->index(['host', 'method', 'ip'], 'idx_host_method_ip');
            $table->comment('请求日志表');
        });
    }

    public function down()
    {
        Schema::connection('admin')->dropIfExists('request_logs');
    }
}
