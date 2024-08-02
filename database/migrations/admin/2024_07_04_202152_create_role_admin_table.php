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
        Schema::connection('admin')->create('role_admin', function (Blueprint $table) {
            $table->foreignId('role_id')->comment("角色ID")->constrained('roles')->onDelete('cascade');
            $table->foreignId('admin_id')->comment("管理员ID")->constrained('admins')->onDelete('cascade');
            $table->primary(['role_id', 'admin_id']);
            $table->comment('用户角色表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('role_admin');
    }
};
