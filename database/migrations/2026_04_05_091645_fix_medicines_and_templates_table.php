<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create medicines table if it doesn't exist
        if (!Schema::hasTable('medicines')) {
            Schema::create('medicines', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['Injection', 'Tablet', 'Capsule', 'Syrup', 'Ointment', 'Drops', 'Inhaler', 'Other']);
                $table->string('company_name');
                $table->string('strength')->nullable(); // e.g., 500mg, 1ml
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['type', 'is_active']);
            });
        }

        // Create surgery_templates table if it doesn't exist
        if (!Schema::hasTable('surgery_templates')) {
            Schema::create('surgery_templates', function (Blueprint $table) {
                $table->id();
                $table->string('template_name');
                $table->json('rx_admission'); // JSON array of medicines for admission
                $table->json('pre_op_orders'); // JSON array of pre-operative orders
                $table->json('post_op_orders'); // JSON array of post-operative orders
                $table->json('investigations'); // JSON array of required investigations
                $table->json('advices')->nullable(); // JSON array of general advices
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable(); // Remove foreign constraint for now
                $table->timestamps();

                $table->index(['template_name', 'is_active']);
            });
        }

        // Insert sample medicines if table is empty
        if (DB::table('medicines')->count() == 0) {
            DB::table('medicines')->insert([
                ['name' => 'Paracetamol', 'type' => 'Tablet', 'company_name' => 'Beximco Pharma', 'strength' => '500mg', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Amoxicillin', 'type' => 'Capsule', 'company_name' => 'Square Pharma', 'strength' => '500mg', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Normal Saline', 'type' => 'Injection', 'company_name' => 'Beximco Pharma', 'strength' => '500ml', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Ceftriaxone', 'type' => 'Injection', 'company_name' => 'Incepta Pharma', 'strength' => '1gm', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Omeprazole', 'type' => 'Capsule', 'company_name' => 'Square Pharma', 'strength' => '20mg', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
