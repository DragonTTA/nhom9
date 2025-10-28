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
        Schema::create('upload_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('max_file_size_mb')->default(10)->comment('Dung lượng tối đa cho mỗi file (MB)');
            $table->string('allowed_types', 255)
                ->default('pdf,doc,docx,xls,xlsx,jpg,jpeg,png')
                ->comment('Các loại file được phép tải lên');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('ID người cập nhật cấu hình');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_settings');
    }
};
