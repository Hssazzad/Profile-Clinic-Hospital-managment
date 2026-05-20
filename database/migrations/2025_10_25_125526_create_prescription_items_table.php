<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->string('medicine_name', 150);
            $table->string('strength', 60)->nullable(); // 500mg, 10ml, etc.
            $table->string('dose', 80)->nullable();     // 1+0+1
            $table->string('route', 60)->nullable();    // oral, topical, IM
            $table->string('frequency', 60)->nullable();// BD/TDS/QID
            $table->string('duration', 60)->nullable(); // 5 days, 2 weeks
            $table->string('timing', 80)->nullable();   // before/after meal
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prescription_items');
    }
};
