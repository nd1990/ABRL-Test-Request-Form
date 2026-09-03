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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('sample_name')->nullable()->after('company_name');
            $table->string('sample_batch_no')->nullable()->after('sample_name');
            $table->string('sample_physical_form')->nullable()->after('sample_batch_no');
            $table->string('sample_storage_condition')->nullable()->after('sample_physical_form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['sample_name', 'sample_batch_no', 'sample_physical_form', 'sample_storage_condition']);
        });
    }
};
