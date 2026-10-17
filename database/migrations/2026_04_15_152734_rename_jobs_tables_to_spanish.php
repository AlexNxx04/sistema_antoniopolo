<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('jobs', 'cola_trabajos');
        Schema::rename('failed_jobs', 'cola_fallidos');
        Schema::rename('job_batches', 'cola_lotes');
    }

    public function down(): void
    {
        Schema::rename('cola_trabajos', 'jobs');
        Schema::rename('cola_fallidos', 'failed_jobs');
        Schema::rename('cola_lotes', 'job_batches');
    }
};
