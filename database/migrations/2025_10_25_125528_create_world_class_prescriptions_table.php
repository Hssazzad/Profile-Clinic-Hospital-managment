<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ১. মূল প্রেসক্রিপশন টেবিল
        Schema::create('world_class_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_no')->unique();
            $table->unsignedBigInteger('patient_id');
            // ৫টি ইনডোর টাইপ + আউটডোর
            $table->enum('prescription_type', ['outdoor', 'admission', 'pre-op', 'post-op', 'fresh', 'discharge']);
            $table->date('prescribed_on');
            $table->string('doctor_name');
            $table->unsignedBigInteger('doctor_id')->nullable();

            // ভাইটালস (O/E) - চিরকুট অনুযায়ী
            $table->integer('bp_systolic')->nullable();
            $table->integer('bp_diastolic')->nullable();
            $table->integer('pulse')->nullable();
            $table->integer('spo2')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();

            // ইনভেস্টিগেশন বা এডভাইস (বাম পাশের কলাম)
            $table->text('investigations')->nullable();

            // স্পেসিফিক নোটস
            $table->text('case_summary')->nullable(); // Case of 38 wks pregnancy...
            $table->text('admission_notes')->nullable();

            // প্রি-অপারেটিভ স্পেসিফিক (Consent, OT Time)
            $table->boolean('consent_taken')->default(false);
            $table->time('ot_time')->nullable();

            // বেবি নোট (Caesarean এর জন্য জরুরি)
            $table->string('baby_sex')->nullable();
            $table->string('baby_weight')->nullable();
            $table->time('birth_time')->nullable();

            // ডিসচার্জ ও পেমেন্ট ক্লিয়ারেন্স লজিক
            $table->boolean('accounts_clearance')->default(false);
            $table->decimal('final_bill_amount', 10, 2)->default(0.00); // পেমেন্ট হিসাবের জন্য
            $table->text('discharge_advice')->nullable();

            $table->timestamps();

            // ফরেন কি
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');

            // ইনডেক্স (ছোট নাম ব্যবহার করা হয়েছে যাতে এরর না আসে)
            $table->index(['patient_id', 'prescription_type'], 'pt_type_idx');
            $table->index('prescribed_on', 'pres_date_idx');
        });

        // ২. মেডিসিন টেবিল (ইনডেক্স নাম ফিক্স করা হয়েছে)
        Schema::create('world_class_prescription_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id');
            $table->string('medicine_name');
            $table->string('dosage');       // e.g., 1+0+1
            $table->string('duration');     // e.g., 7 days
            $table->string('instruction')->nullable(); // e.g., আহারের পর
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // ফরেন কি উইথ কাস্টম নেম (To avoid long identifier error)
            $table->foreign('prescription_id', 'wcp_med_pres_fk')
                  ->references('id')->on('world_class_prescriptions')
                  ->onDelete('cascade');

            // ইনডেক্স উইথ কাস্টম নেম
            $table->index(['prescription_id', 'sort_order'], 'wcp_med_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('world_class_prescription_medicines');
        Schema::dropIfExists('world_class_prescriptions');
    }
};
