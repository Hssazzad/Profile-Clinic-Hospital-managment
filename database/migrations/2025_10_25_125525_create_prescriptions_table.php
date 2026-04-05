<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('prescription_no', 30)->unique(); // e.g., RX20251025-0001
            $table->date('prescribed_on')->nullable();
            $table->string('doctor_name', 120)->nullable();
            $table->string('doctor_reg_no', 60)->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('advices')->nullable();             // general advice / lifestyle
            $table->text('investigations')->nullable();      // lab tests
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prescriptions');
    }
};
