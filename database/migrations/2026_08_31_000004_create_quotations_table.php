<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->string('client_name');
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('gst_number')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->string('pdf_path')->nullable();
            $table->string('email_status')->default('pending');
            $table->timestamp('email_sent_at')->nullable();
            $table->text('project_description')->nullable();
            $table->text('additional_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->string('expected_timeline')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('email_status');
            $table->index('quotation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
