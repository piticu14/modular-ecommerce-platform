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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn('product_id');
            $table->uuid('product_uuid')->after('order_id');
            $table->unique('product_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_uuid');
            $table->unsignedBigInteger('product_id')->index();
            $table->index(['order_id', 'product_id']);
        });
    }
};
