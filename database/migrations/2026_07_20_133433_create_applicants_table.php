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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone');
            $table->unsignedBigInteger('current_salary')->nullable();
            $table->unsignedBigInteger('expected_salary');
            $table->string('notice_period');
            $table->string('cv_path')->nullable();
            $table->string('status')->index();
            $table->text('hr_note')->nullable();
            $table->dateTime('applied_at')->index();
            $table->timestamps();

            $table->index(['job_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
