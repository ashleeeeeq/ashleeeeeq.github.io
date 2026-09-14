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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('name_extension', 10)->nullable();
            $table->date('birth_date');
            $table->enum('sex', ['male', 'female']);
            $table->string('contact_number');
            $table->string('dial_code', 6)->nullable();
            $table->boolean('form_given')->nullable();
            $table->boolean('with_disability')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('beneficiary_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->enum('guardian_type', ['mother', 'father', 'guardian']);
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('civil_status', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('place_of_birth', 80)->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('contact_number', 20);
            $table->string('dial_code', 6)->nullable();
            $table->enum('highest_education', ['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'])->nullable();
            $table->string('job', 50)->nullable();
            $table->integer('estimated_salary')->nullable();
            $table->boolean('deceased')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('education_intake_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->integer('age');
            $table->string('place_of_birth', 80);
            $table->integer('number_of_siblings');
            $table->integer('older_sibling_age')->nullable();
            $table->integer('younger_sibling_age')->nullable();
            $table->string('civil_status', 50);
            $table->enum('highest_education', ['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad']);
            $table->string('school_name', 100);
            $table->enum('grade_level', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']);
            $table->boolean('other_scholarship')->default(0);
            $table->text('scholarship_org_question')->nullable();
            $table->text('hardworking_question');
            $table->text('dream_question');
            $table->text('scholarship_question');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->timestamps();
        });

        Schema::create('beneficiary_status_types', function (Blueprint $table) {
            $table->id();
            $table->string('status_name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('beneficiary_status_type_program', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_status_type_id');
            $table->unsignedBigInteger('program_id');
            $table->timestamps();

            $table->foreign('beneficiary_status_type_id', 'fk_bstp_type_id')->references('id')->on('beneficiary_status_types')->cascadeOnDelete();
            $table->foreign('program_id', 'fk_bstp_program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->unique(['beneficiary_status_type_id', 'program_id'], 'unique_status_type_program');
        });

        Schema::create('beneficiary_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beneficiary_status_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['beneficiary_id', 'beneficiary_status_type_id', 'start_date'], 'unique_beneficiary_status');
            $table->index(['beneficiary_status_type_id', 'start_date', 'end_date'],'beneficiary_statuses_type_dates_index');
            $table->index(['beneficiary_id', 'start_date', 'end_date'], 'beneficiary_statuses_beneficiary_dates_index');
        });

        Schema::create('beneficiary_program_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            // history metadata: allow multiple rows per beneficiary/program
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('exited_at')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['program_id', 'beneficiary_id'], 'beneficiary_program_memberships_program_beneficiary_index');
            $table->index(['beneficiary_id', 'program_id'], 'beneficiary_program_memberships_beneficiary_program_index');
        });

        Schema::create('education_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->string('school_name', 150);
            $table->date('academic_year_start_date');
            $table->date('academic_year_end_date');
            $table->enum('education_level', ['elementary', 'high_school', 'shs', 'college']);
            $table->enum('grade_level', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']);
            $table->enum('enrollment_status', ['active', 'completed', 'transferred', 'dropped', 'withdrawn', 'repeated'])->default('active');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['beneficiary_id', 'academic_year_start_date'], 'education_enrollments_beneficiary_start_index');
            $table->index(['beneficiary_id', 'grade_level'], 'education_enrollments_beneficiary_grade_index');
            $table->index(['enrollment_status', 'academic_year_start_date'], 'education_enrollments_status_start_index');
            $table->index(['academic_year_start_date', 'academic_year_end_date'], 'education_enrollments_year_range_index');
        });

        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('education_enrollment_id')
                ->nullable()
                ->constrained('education_enrollments')
                ->cascadeOnDelete();
            $table->enum('term', ['1st', '2nd', '3rd', '4th']);
            $table->decimal('gwa', 5, 2);
            $table->decimal('school_attendance', 5, 2);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at', 'academic_records_created_at_index');
            $table->index('beneficiary_id', 'academic_records_beneficiary_id_index');
            $table->index('education_enrollment_id', 'academic_records_enrollment_id_index');
            $table->unique(['education_enrollment_id', 'term'], 'academic_records_enrollment_term_unique');
        });

        Schema::create('subject_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_record_id')->constrained()->cascadeOnDelete();
            $table->string('subject_name');
            $table->decimal('grade', 5, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assessment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_name', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ffa_assessment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('assessment_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100)->nullable();
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('remarks', 100)->nullable();
            $table->date('date');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assessment_category_id', 'date'], 'ffa_assessment_records_category_date_index');
            $table->index(['beneficiary_id', 'assessment_category_id'], 'ffa_assessment_records_beneficiary_category_index');
        });

        Schema::create('injury_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete()->nullable();
            $table->string('injury_type', 100);
            $table->enum('severity', ['minor', 'moderate', 'serious', 'severe', 'critical']);
            $table->string('body_part', 100);
            $table->enum('status', ['recovering', 'recovered', 'chronic']);
            $table->string('remarks', 100)->nullable();
            $table->date('recovery_start_date');
            $table->date('recovery_end_date')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('injury_records');
        Schema::dropIfExists('ffa_assessment_records');
        Schema::dropIfExists('assessment_categories');
        Schema::dropIfExists('subject_grades');
        Schema::dropIfExists('academic_records');
        Schema::dropIfExists('education_enrollments');
        Schema::dropIfExists('beneficiary_program_memberships');
        Schema::dropIfExists('beneficiary_statuses');
        Schema::dropIfExists('beneficiary_status_types');
        Schema::dropIfExists('education_intake_sheets');
        Schema::dropIfExists('beneficiary_guardians');
        Schema::dropIfExists('beneficiaries');
        Schema::dropIfExists('programs');
    }
};