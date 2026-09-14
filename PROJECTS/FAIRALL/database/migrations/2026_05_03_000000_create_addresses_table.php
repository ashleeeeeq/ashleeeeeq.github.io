<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_id')->nullable()->unique()->constrained('staff')->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->unique()->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('guardian_id')->nullable()->unique()->constrained('beneficiary_guardians')->cascadeOnDelete();
            $table->string('address_line', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->timestamps();
        });

        Schema::table('staff', function (Blueprint $table): void {
            if (Schema::hasColumn('staff', 'address')) {
                $table->dropColumn('address');
            }
        });

        Schema::table('beneficiaries', function (Blueprint $table): void {
            if (Schema::hasColumn('beneficiaries', 'address')) {
                $table->dropColumn('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table): void {
            $table->string('address', 255)->nullable();
        });

        Schema::table('beneficiaries', function (Blueprint $table): void {
            $table->string('address')->nullable();
        });

        Schema::dropIfExists('addresses');
    }
};
