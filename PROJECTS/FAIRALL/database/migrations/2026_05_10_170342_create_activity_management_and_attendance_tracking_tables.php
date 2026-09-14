<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // initial values 'Futsal', 'Badminton', 'Basketball', 'Volleyball'
        Schema::create('sport_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        // initial values 'EQ Session', 'Tutorial Session', 'Training Session'
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['program_id', 'name'], 'activity_types_program_name_index');
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('sport_type_id')->nullable()->constrained('sport_types')->cascadeOnDelete();
            $table->foreignId('activity_type_id')->constrained('activity_types')->cascadeOnDelete();
            $table->string('description', 100)->nullable();
            $table->boolean('is_active');
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

            $table->index(['program_id', 'activity_type_id'], 'activities_program_activity_type_index');
            $table->index(['program_id', 'sport_type_id'], 'activities_program_sport_type_index');
        });

        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->dateTime('joined_at');
            $table->dateTime('exited_at')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['activity_id', 'joined_at'],'activity_participants_activity_joined_index');
            $table->index(['beneficiary_id', 'joined_at'], 'activity_participants_beneficiary_joined_index');
            $table->index(['beneficiary_id', 'exited_at'], 'activity_participants_beneficiary_exited_index');
        });


        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('program_id')->nullable()->constrained('programs')->cascadeOnDelete();
            $table->foreignId('event_type_id')->constrained('event_types')->cascadeOnDelete();
            $table->string('qr_token')->nullable()->unique();
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
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

        Schema::create('activity_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->datetime('schedule');
            $table->string('qr_token')->nullable()->unique();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['activity_id', 'schedule'], 'activity_sessions_activity_schedule_index');
            $table->index(['schedule'], 'activity_sessions_schedule_index');
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('activity_session_id')->nullable()->constrained('activity_sessions')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->enum('attendance_status', ['present', 'absent', 'late', 'excused']);
            $table->string('remarks')->nullable();
            $table->enum('attendance_method', ['QR', 'Staff']);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['staff_id', 'activity_session_id']);
            $table->unique(['staff_id', 'event_id']);
            $table->unique(['beneficiary_id', 'activity_session_id']);
            $table->unique(['beneficiary_id', 'event_id']);

            $table->index(['activity_session_id', 'attendance_status'], 'attendances_session_status_index');
            $table->index(['beneficiary_id', 'attendance_status'], 'attendances_beneficiary_status_index');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_single_attendee CHECK ((staff_id IS NULL) <> (beneficiary_id IS NULL))");
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_single_context CHECK ((activity_session_id IS NULL) <> (event_id IS NULL))");
        }

        Schema::create('home_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->cascadeOnDelete();
            $table->enum('visit_type', ['routine', 'intervention', 'follow-up']);
            $table->string('purpose', 100);
            $table->string('notes', 500)->nullable();
            $table->datetime('schedule');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->nullOnDelete();
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

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->enum('type', ['sports & athletic', 'academic', 'technical & professional', 'creative & leisure']);
            $table->enum('scale', ['local', 'regional', 'national', 'international']);
            $table->string('organizer');
            $table->string('description')->nullable();
            $table->string('venue');
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
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

            $table->index(['program_id', 'start'], 'competitions_program_start_index');
        });

        Schema::create('competition_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete()->nullable();
            $table->string('placement');
            $table->date('date_given');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete(); // which staff created
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['competition_id', 'date_given'], 'competition_results_competition_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_results');
        Schema::dropIfExists('competitions');
        Schema::dropIfExists('home_visits');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('activity_sessions');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_types');
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('activity_types');
        Schema::dropIfExists('sport_types');
    }
};
