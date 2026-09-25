<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('invoice_status')->default('pending')->after('notes');
            $table->foreignId('invoice_id')->nullable()->nullOnDelete()->after('invoice_status');
            $table->timestamp('invoiced_at')->nullable()->after('invoice_id');

            $table->index('invoice_status');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropIndex(['invoice_status']);
            $table->dropColumn(['invoice_status', 'invoice_id', 'invoiced_at']);
        });
    }
};