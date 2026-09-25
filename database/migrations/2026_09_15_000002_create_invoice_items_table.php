<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_item_id')->nullable()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->nullOnDelete();
            $table->foreignId('lab_test_id')->nullable()->nullOnDelete();
            $table->string('service_name_snapshot');
            $table->text('description_snapshot')->nullable();
            $table->string('unit_snapshot')->default('unit');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_snapshot', 12, 2)->default(0);
            $table->decimal('discount_snapshot', 12, 2)->default(0);
            $table->decimal('tax_percentage_snapshot', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('quotation_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};