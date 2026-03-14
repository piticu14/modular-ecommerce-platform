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
            $table->uuid()->unique();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('status', 32)->default('PENDING');
            $table->string('currency', 3)->default('CZK');

            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('total') ->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
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
