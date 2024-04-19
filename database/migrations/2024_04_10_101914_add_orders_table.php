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
            $table->string('email');
            $table->string("ip_address")->nullable();
            $table->integer("product_id");
            $table->integer("quantity");
            $table->foreignId("user_id")->constrained("users");
            $table->enum("status" ,["pending" , "confirmed" , "completed"])->default("pending");
            $table->enum("payment_method" ,["online", "cash"]);
            $table->foreignId("deliveriesId")->constrained("deliveries");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
