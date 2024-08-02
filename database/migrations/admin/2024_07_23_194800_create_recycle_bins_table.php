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
        Schema::connection('admin')->create('recycle_bins', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id')->default(0)->comment('管理员ID');
            $table->string('table_name', 100)->default('')->comment('表名');
            $table->unsignedInteger('table_id')->default(0)->comment('表主键ID');
            $table->json('content')->comment('内容');
            $table->timestamps();
            $table->comment('回收站表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('recycle_bins');
    }
};
