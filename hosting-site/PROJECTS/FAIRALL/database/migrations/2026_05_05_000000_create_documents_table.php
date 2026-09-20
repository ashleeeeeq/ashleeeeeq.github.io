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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->string('display_name'); // User-friendly name for the file
            $table->string('file_path'); // Path in S3
            $table->string('file_type'); // MIME type (e.g., application/pdf, image/png)
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('source_module')->nullable(); // Which module uploaded it (e.g., 'academic_record', 'ffa_assessment', 'injury_record', 'manual')
            $table->unsignedBigInteger('source_record_id')->nullable(); // ID of the related record (e.g., academic_record_id)
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Indexes
            $table->index('beneficiary_id');
            $table->index(['source_module', 'source_record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
