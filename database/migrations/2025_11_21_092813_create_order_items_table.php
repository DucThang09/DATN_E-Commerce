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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // khóa ngoại tới orders
            $table->unsignedBigInteger('order_id');

            // khóa ngoại tới products (nếu có)
            $table->unsignedBigInteger('product_id')->nullable();

            // copy thông tin tại thời điểm mua
            $table->string('product_name');
            $table->string('product_image')->nullable();

            $table->unsignedInteger('quantity');              // số lượng
            $table->unsignedBigInteger('unit_price');         // giá 1 sp
            $table->unsignedBigInteger('total_price');        // quantity * unit_price

            $table->timestamps();

            // ràng buộc quan hệ
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
