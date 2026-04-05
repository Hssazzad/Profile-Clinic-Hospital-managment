<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patientcode', 30)->unique();      // e.g., P20251025-0001
            $table->string('patientname', 150);
            $table->string('address', 255)->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('mobile_no', 20)->nullable()->index();
            $table->string('contact_no', 20)->nullable();
            $table->string('nid_number', 30)->nullable()->unique();

            // Other useful fields
            $table->string('email', 120)->nullable()->index();
            $table->enum('gender', ['Male','Female','Other'])->nullable();
            $table->string('blood_group', 5)->nullable();     // A+, O-, etc.
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('patients');
    }
};
