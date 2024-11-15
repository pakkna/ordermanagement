<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // References the user placing the order
            $table->decimal('total_amount', 10, 2); // Total amount of the order
            $table->string('status')->default('pending'); // Order status (e.g., pending, completed, cancelled)
            $table->string('payment_status')->default('unpaid'); // Payment status (e.g., paid, unpaid, refunded)
            $table->string('payment_method')->nullable(); // Payment method used (e.g., credit card, PayPal, etc.)
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
