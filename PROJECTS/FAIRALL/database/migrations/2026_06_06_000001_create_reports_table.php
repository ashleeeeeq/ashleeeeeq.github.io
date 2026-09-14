<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // ReportType enum value
            $table->string('period_type'); // ReportPeriod enum value
            $table->json('period_config'); // {year, quarter?, from_year?, to_year?}
            $table->date('date_from');
            $table->date('date_to');
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // ReportStatus enum value
            $table->string('generation_phase')->nullable(); // consolidating / generating_narratives / rendering_pdf
            $table->json('narrative_cache')->nullable();
            $table->json('boot_payload')->nullable();
            $table->string('pdf_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'created_at'], 'reports_type_status_created_index');
        });

        Schema::create('dashboard_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('metric');          // e.g. 'active_scholars', 'donations_total'
            $table->string('dimension')->nullable(); // e.g. 'elementary', 'soccer', null for non-dimensional
            $table->smallInteger('year');
            $table->tinyInteger('month')->nullable(); // null for annual-only metrics
            $table->decimal('value', 16, 2)->default(0);
            $table->timestamp('computed_at')->nullable();

            $table->unique(['program_id', 'year', 'month', 'metric', 'dimension'], 'dashboard_metrics_unique');
            $table->index(['program_id', 'year', 'month'], 'dashboard_metrics_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_metrics');
        Schema::dropIfExists('reports');
    }
};
