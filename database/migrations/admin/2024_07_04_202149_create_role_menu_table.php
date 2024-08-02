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
        Schema::connection('admin')->create('role_menu', function (Blueprint $table) {
            $table->foreignId('role_id')->comment("角色ID")->constrained('roles')->onDelete('cascade');
            $table->foreignId('menu_id')->comment("菜单ID")->constrained('menus')->onDelete('cascade');
            $table->primary(['role_id', 'menu_id']);
            $table->comment('角色菜单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('role_menu');
    }
};
