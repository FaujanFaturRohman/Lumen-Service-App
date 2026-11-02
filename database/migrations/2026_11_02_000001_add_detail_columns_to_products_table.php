<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDetailColumnsToProductsTable extends Migration
{
    /**
     * Menambah kolom detail produk (sku, description, weight_gram, is_available)
     * agar tabel products sama dengan project lumen-products.
     * Kolom dibuat nullable/berdefault supaya data lama tetap aman.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 30)->nullable()->unique()->after('id');
            $table->text('description')->nullable()->after('category');
            $table->unsignedInteger('weight_gram')->default(1)->after('stock');
            $table->boolean('is_available')->default(true)->after('weight_gram');
        });

        // Isi sku untuk data lama: SKU-001, SKU-002, dst.
        foreach (DB::table('products')->whereNull('sku')->pluck('id') as $id) {
            DB::table('products')->where('id', $id)->update([
                'sku' => 'SKU-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'description', 'weight_gram', 'is_available']);
        });
    }
}
