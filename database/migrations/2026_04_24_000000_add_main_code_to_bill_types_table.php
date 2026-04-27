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
        if (!Schema::hasTable('bill_types') || Schema::hasColumn('bill_types', 'main_code')) {
            return;
        }

        Schema::table('bill_types', function (Blueprint $table) {
            $table->integer('main_code')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('bill_types') || !Schema::hasColumn('bill_types', 'main_code')) {
            return;
        }

        Schema::table('bill_types', function (Blueprint $table) {
            $table->dropColumn('main_code');
        });
    }
};
