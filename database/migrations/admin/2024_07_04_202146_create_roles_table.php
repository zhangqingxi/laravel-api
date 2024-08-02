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
        Schema::connection('admin')->create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique()->default("")->comment("角色名称");
            $table->unsignedtinyInteger('status')->default(0)->comment("角色状态 1：启用 0：禁用");
            $table->string('remark', 150)->default("")->comment("角色备注");
            $table->timestamps();
            $table->softDeletes();
            $table->comment('角色表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('roles');
    }
};
