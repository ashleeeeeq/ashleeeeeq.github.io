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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('organization_name', 100)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('contact_number');
            $table->string('dial_code', 6)->nullable();
            $table->enum('donor_type', ['individual', 'organization']);
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

        Schema::create('grants', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name', 150);
            $table->string('email', 150);
            $table->string('contact_number')->nullable();
            $table->string('dial_code', 6)->nullable();
            $table->string('grant_name', 150);
            $table->text('description')->nullable();
            $table->decimal('total_amount', 14, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
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

            $table->index(['start_date', 'end_date'], 'grants_start_end_index');
        });

        Schema::create('grant_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->constrained('grants')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['grant_id', 'program_id']);
            $table->index(['program_id', 'grant_id'], 'grant_program_program_grant_index');
        });

        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('grant_id')->nullable()->constrained('grants')->nullOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['grant_id', 'end_date'], 'deliverables_grant_end_index');
            $table->index(['donor_id', 'end_date'], 'deliverables_donor_end_index');
            $table->index(['completed_at', 'end_date'], 'deliverables_completed_end_index');
        });

        // Subscriptions (recurring donations via PayPal)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('paypal_subscription_id')->nullable()->unique();
            $table->string('plan_id')->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->enum('status', ['pending', 'active', 'paused', 'cancelled', 'expired'])->default('pending');
            $table->date('next_billing_date')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });

        // Checkout sessions for provider-hosted flows
        Schema::create('checkout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->enum('gateway', ['xendit', 'paypal'])->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 10)->default(env('PAYMENT_CURRENCY', 'USD'));
            $table->string('provider_session_id', 150)->nullable();
            $table->string('return_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->enum('status', ['initiated', 'redirected', 'completed', 'failed', 'cancelled'])->default('initiated');
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->index(['program_id', 'status'], 'checkout_sessions_program_status_index');
        });

        // Donations ledger (canonical donation records)
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->foreignId('checkout_session_id')->nullable()->constrained('checkout_sessions')->nullOnDelete();
            $table->enum('gateway', ['manual', 'xendit', 'paypal'])->nullable();
            $table->string('gateway_reference', 150)->nullable();
            $table->string('reference_number', 150)->nullable();
            $table->string('receipt_number', 150)->nullable();
            $table->string('receipt_path')->nullable();
            $table->enum('donation_type', ['financial', 'in-kind'])->default('financial');
            $table->decimal('amount', 14, 2);
            $table->string('currency', 10)->default(env('PAYMENT_CURRENCY', 'USD'));
            $table->dateTime('transaction_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // ensure idempotent upserts using gateway + gateway_reference
            $table->unique(['gateway', 'gateway_reference'], 'donations_gateway_reference_unique');
            $table->unique('reference_number', 'donations_reference_number_unique');
            $table->index(['program_id', 'status', 'transaction_date'], 'donations_program_status_transaction_index');
            $table->index(['donor_id', 'status'], 'donations_donor_status_index');
        });

        // Receipts for donations (PDFs stored on disk)
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->string('path');
            $table->string('hash', 128)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Allocations: where donations/grants are assigned to beneficiaries
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
            $table->foreignId('grant_id')->nullable()->constrained('grants')->nullOnDelete();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount_cents')->unsigned();
            $table->date('date_allocated');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['beneficiary_id', 'date_allocated'], 'allocations_beneficiary_date_index');
            $table->index(['grant_id', 'date_allocated'], 'allocations_grant_date_index');
        });

        // Funding targets per program/year
        Schema::create('funding_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->integer('year')->index();
            $table->bigInteger('target_amount_cents')->unsigned();
            $table->char('currency', 3)->default(env('PAYMENT_CURRENCY', 'USD'));
            $table->text('notes')->nullable();
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

            $table->index(['program_id', 'year'], 'funding_targets_program_year_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_targets');
        Schema::dropIfExists('allocations');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('checkout_sessions');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('deliverables');
        Schema::dropIfExists('grant_program');
        Schema::dropIfExists('grants');
        Schema::dropIfExists('donors');
    }
};