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
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('product_id')->index();

            $table->string('product_name');
            $table->unsignedBigInteger('unit_price');
            $table->string('currency', 3);
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
