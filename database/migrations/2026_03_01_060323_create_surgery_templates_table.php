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
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['template_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgery_templates');
    }
};
