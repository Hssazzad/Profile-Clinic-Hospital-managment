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
        Schema::create('admission_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_no')->unique();
            $table->unsignedBigInteger('patient_id');
            $table->enum('type', ['pre-op', 'post-op', 'fresh', 'discharge']);
            $table->date('prescription_date');
            $table->string('doctor_name');
            $table->text('primary_diagnosis');
            $table->text('secondary_diagnosis')->nullable();
            $table->text('final_diagnosis')->nullable();
            $table->text('lab_investigations')->nullable();
            $table->text('radiology')->nullable();
            $table->text('other_investigations')->nullable();
            $table->text('medications');
            $table->text('pre_op_instructions')->nullable();
            $table->text('post_op_instructions')->nullable();
            $table->text('follow_up')->nullable();
            $table->text('discharge_advice')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->date('discharge_date')->nullable();
            $table->enum('discharge_type', ['normal', 'lama', 'referred', 'death'])->nullable();
            $table->enum('anesthesia_clearance', ['fit', 'high-risk', 'optimized'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('admissions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['patient_id', 'type']);
            $table->index('prescription_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_prescriptions');
    }
};
