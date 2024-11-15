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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Reference to the order this item belongs to
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Reference to the product being ordered
            $table->integer('quantity'); // Quantity of the product ordered
            $table->decimal('price', 10, 2); // Price of the product at the time of the order
            $table->decimal('total', 10, 2); // Total for this item (price * quantity)
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
        Schema::dropIfExists('order_items');
    }
};
