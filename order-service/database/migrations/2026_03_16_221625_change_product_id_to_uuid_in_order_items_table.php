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

            $table->dropColumn('product_id');

            $table->uuid('product_uuid')->nullable()->after('order_id');
        });

        DB::statement('UPDATE order_items SET product_uuid = UUID() WHERE product_uuid IS NULL');

        Schema::table('order_items', function (Blueprint $table) {
            $table->uuid('product_uuid')->nullable(false)->change();
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
        });
    }
};
