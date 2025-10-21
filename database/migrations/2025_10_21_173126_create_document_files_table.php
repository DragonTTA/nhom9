<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id'); // id công văn
            $table->string('name'); // tên file
            $table->string('path'); // đường dẫn lưu file
            $table->string('type'); // pdf, jpg, png, ...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_files');
    }
};

