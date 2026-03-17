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
        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->uuid('order_uuid')->nullable()->after('id');
            $table->uuid('order_item_uuid')->nullable()->after('id');

            $table->dropColumn('order_id');
            $table->dropColumn('order_item_id');

        });

        DB::statement('UPDATE stock_reservations SET order_uuid = UUID() WHERE order_uuid IS NULL');
        DB::statement('UPDATE stock_reservations SET order_item_uuid = UUID() WHERE order_item_uuid IS NULL');

        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->uuid('order_uuid')->nullable(false)->change();
            $table->uuid('order_item_uuid')->nullable(false)->change();
            $table->index('order_uuid');
            $table->index('order_item_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->dropIndex(['order_uuid']);
            $table->dropIndex(['order_item_uuid']);
            $table->dropColumn(['order_uuid', 'order_item_uuid']);

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unique(['order_item_id']);
        });
    }
};
