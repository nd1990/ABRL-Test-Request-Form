<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('nabl_type')->index();
            $table->string('discipline')->index();
            $table->text('material')->nullable();
            $table->string('parameter');
            $table->string('method')->nullable();
            $table->string('sample_quantity')->nullable();
            $table->string('lead_time')->nullable();
            $table->decimal('charges_per_sample', 12, 2)->default(0);
            $table->string('protocol_no')->nullable();
            $table->string('nabl_range')->nullable();
            $table->string('limit_of_quantification')->nullable();
            $table->text('remarks')->nullable();
            $table->text('protocol_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['nabl_type', 'discipline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
