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
        Schema::connection('admin')->create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->default('')->comment('文件名');
            $table->string('drive', 15)->default('')->comment('驱动 - 存放的配置驱动');
            $table->enum('type', ['image', 'doc', 'video'])->default('image')->comment('文件类型');
            $table->string('mime_type', 50)->default('')->comment('文件的 MIME 类型');
            $table->string('path')->default('')->comment('文件地址');
            $table->unsignedBigInteger('size')->default(0)->comment('文件大小 [字节]');
            $table->string('size_text', 20)->default('')->comment('文件大小 [字节]');
            $table->string('extension', 50)->default('')->comment('文件扩展名');
            $table->string('hash', 100)->default('')->comment('文件哈希值');
            $table->unsignedtinyInteger('uploaded_by')->default(0)->comment('上传文件的用户ID');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('文件资源表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('files');
    }
};
