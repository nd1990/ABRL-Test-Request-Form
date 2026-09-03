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
            $table->string('pan_number')->nullable()->after('gst_number');
            $table->text('courier_address')->nullable()->after('address');
            $table->string('courier_address_line2')->nullable()->after('courier_address');
            $table->string('courier_city')->nullable()->after('courier_address_line2');
            $table->string('courier_state')->nullable()->after('courier_city');
            $table->string('courier_postal_code')->nullable()->after('courier_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['pan_number', 'courier_address', 'courier_address_line2', 'courier_city', 'courier_state', 'courier_postal_code']);
        });
    }
};
