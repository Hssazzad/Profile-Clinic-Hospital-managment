<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportSqlDumpU972011074VzeTw extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will execute the raw SQL from database/sql/u972011074_vzeTw.sql
     * Place the dump file at that path before running `php artisan migrate`.
     *
     * @return void
     */
    public function up()
    {
        $path = database_path('sql/u972011074_vzeTw.sql');

        if (!file_exists($path)) {
            throw new \Exception("SQL dump not found at {$path}. Copy the file to database/sql/u972011074_vzeTw.sql and re-run migrations.");
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \Exception("Failed to read SQL dump at {$path}.");
        }

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * No automatic rollback is provided because the dump contains many tables.
     * Implement manual cleanup if required.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left blank.
    }
}
