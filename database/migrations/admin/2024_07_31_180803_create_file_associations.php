<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('admin')->create('file_associations', function (Blueprint $table) {
            $table->foreignId('file_id')->comment("文件ID")->constrained('files')->onDelete('cascade');
            $table->unsignedTinyInteger('model_id')->comment("模型ID");
            $table->string('model_name',50)->comment("模型名称");
            $table->primary(['file_id', 'model_id', 'model_name']);
            $table->comment('文件关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('file_associations');
    }
};
