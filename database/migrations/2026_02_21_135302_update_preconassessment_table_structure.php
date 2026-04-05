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
        Schema::table('preconassessment', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('preconassessment', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'height')) {
                $table->decimal('height', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'temp')) {
                $table->decimal('temp', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'bp_sys')) {
                $table->integer('bp_sys')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'bp_dia')) {
                $table->integer('bp_dia')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'pulse')) {
                $table->integer('pulse')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'spo2')) {
                $table->integer('spo2')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'rr')) {
                $table->integer('rr')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'code')) {
                $table->string('code')->nullable();
            }
            if (!Schema::hasColumn('preconassessment', 'value')) {
                $table->string('value')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
