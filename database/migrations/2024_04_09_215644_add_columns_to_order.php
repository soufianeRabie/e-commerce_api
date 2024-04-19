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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId("user_id")->nullable()->change()->constrained("users");
            $table->enum("status" ,["pending" , "confirmed" , "completed"])->default("pending");
            $table->enum("payment_method" ,["online", "cash"]);
            $table->decimal("amount" , 10 , 4);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            //
        });
    }
};
