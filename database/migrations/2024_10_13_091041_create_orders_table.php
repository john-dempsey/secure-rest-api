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
            $table->foreignId('customer_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->date('confirmed_date')->nullable()->default(null);
            $table->date('cancelled_date')->nullable()->default(null);
            $table->date('shipped_date')->nullable()->default(null);
            $table->date('payment_date')->nullable()->default(null);
            $table->enum('status', ['received', 'confirmed', 'shipped', 'paid', 'cancelled']);
            $table->timestamps();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->foreignId('product_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
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
