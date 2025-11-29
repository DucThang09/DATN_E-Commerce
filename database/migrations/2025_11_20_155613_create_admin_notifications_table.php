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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');          // Tiêu đề
            $table->text('message')->nullable(); // Nội dung ngắn
            $table->string('type')->nullable();  // ví dụ: user_registered, order_created
            $table->json('data')->nullable();    // lưu thêm user_id, order_id...
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
