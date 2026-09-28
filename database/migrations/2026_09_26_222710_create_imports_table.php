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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 80);
            $table->unsignedInteger('total_records');
            $table->unsignedInteger('successful_records');
            $table->unsignedInteger('failed_records');
            $table->enum('status', ['success', 'partial', 'failed']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
