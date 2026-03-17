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
            $table->uuid()->nullable()->after('id');
        });

        DB::table('order_items')
            ->whereNull('uuid')
            ->orderBy('id')
            ->chunkById(100, function ($orderItems) {
                foreach ($orderItems as $orderItem) {
                    DB::table('order_items')
                        ->where('id', $orderItem->id)
                        ->update([
                            'uuid' => (string) Str::uuid(),
                        ]);
                }
            });

        Schema::table('order_items', function (Blueprint $table) {
            $table->uuid()->nullable(false)->change();
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
