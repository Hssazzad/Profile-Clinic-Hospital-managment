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
        Schema::create('patient_vitals', function (Blueprint $table) {
            $table->id();
            $table->string('patientcode')->index();
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->integer('bp_systolic')->nullable(); // mmHg
            $table->integer('bp_diastolic')->nullable(); // mmHg
            $table->integer('heart_rate')->nullable(); // bpm
            $table->integer('spo2')->nullable(); // percentage
            $table->decimal('temperature', 4, 1)->nullable(); // Fahrenheit
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patientcode', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_vitals');
    }
};
