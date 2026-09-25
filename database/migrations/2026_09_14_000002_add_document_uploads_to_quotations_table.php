<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('msds_report_path')->nullable()->after('preferred_contact_method');
            $table->string('msds_report_name')->nullable()->after('msds_report_path');
            $table->json('other_documents')->nullable()->after('msds_report_name');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['msds_report_path', 'msds_report_name', 'other_documents']);
        });
    }
};
