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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');                      // Product name
            $table->string('slug')->unique();             // SEO-friendly URL
            $table->decimal('price', 10, 2);              // Regular price
            $table->integer('stock');                     // Current stock level
            $table->text('description')->nullable();      // Detailed description of the product
            $table->string('status')->default('active');  // Status indicator (e.g., active, discontinued)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
