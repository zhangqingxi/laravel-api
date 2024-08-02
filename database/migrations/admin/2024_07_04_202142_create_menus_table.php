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
        Schema::connection('admin')->create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique()->default('')->comment('菜单名称');
            $table->string('path', 80)->default("")->comment("路径");
            $table->string('component', 80)->default("")->comment("组件");
            $table->string('route', 80)->default("")->comment("路由");
            $table->string('icon', 50)->default("")->comment("图标");
            $table->foreignId('pid')->nullable()->comment("父级菜单ID")->constrained('menus')->onDelete('cascade');
            $table->boolean('visible')->default(true)->comment('是否显示');
            $table->unsignedtinyInteger('status')->default(0)->comment("菜单状态 1：启用 0：禁用");
            $table->unsignedtinyInteger('sort')->default(0)->comment("排序");
            $table->timestamps();
            $table->index(['name', 'pid'], 'idx_name_pid');
            $table->softDeletes();
            $table->comment('菜单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('menus');
    }
};
