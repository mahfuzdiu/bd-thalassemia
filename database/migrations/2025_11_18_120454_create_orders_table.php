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
            $table->string('order_number')->unique(); // e.g., ORD-20251118-001
            $table->foreignId('user_id')->constrained();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cod', 'stripe', 'paypal'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

            //not normalized for project simplification
            $table->enum('shipping_method', ['standard', 'sundarban', 'fast_courier']);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->text('shipping_address');
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
