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
        Schema::table('products', function (Blueprint $table) {
            // thêm cột, để nullable để còn đổ dữ liệu
            $table->unsignedBigInteger('category_id')->nullable()->after('category');

            // nếu muốn thêm khóa ngoại:
            $table->foreign('category_id')
                  ->references('category_id')   // PK bên categories
                  ->on('categories')
                  ->onDelete('set null');       // nếu xóa category thì set null
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
