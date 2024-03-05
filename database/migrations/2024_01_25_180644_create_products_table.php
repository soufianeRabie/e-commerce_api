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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title' ,40);
            $table->string('description',300);
            $table->string('image',);
            $table->string('rating');
            $table->decimal('price' ,10 ,2)->change();
            $table->decimal('oldPrice' , 10 , 2)->nullable()->change();
            $table->boolean('isSold');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
