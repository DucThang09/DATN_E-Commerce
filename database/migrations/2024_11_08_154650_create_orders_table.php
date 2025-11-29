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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id'); 
            $table->string('name'); 
            $table->string('number', 20)->nullable(); 
            $table->string('email', 255); 
            $table->string('method', 50); 
            $table->text('address'); 
            $table->string('total_products',100); 
            $table->decimal('total_price', 10, 2); 
            $table->date('placed_on'); 
            $table->enum('payment_status', ['pending', 'completed', 'canceled'])->default('pending'); 
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
