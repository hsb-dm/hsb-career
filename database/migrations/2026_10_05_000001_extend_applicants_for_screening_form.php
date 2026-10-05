<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->unsignedBigInteger('expected_salary')->nullable()->change();
            $table->string('notice_period')->nullable()->change();
            $table->unsignedTinyInteger('current_age')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('current_status')->nullable();
            $table->string('current_domicile')->nullable();
            $table->string('english_fluency')->nullable();
            $table->string('foreign_language_fluency')->nullable();
            $table->string('current_salary_answer')->nullable();
            $table->text('additional_benefits')->nullable();
            $table->string('expected_salary_answer')->nullable();
            $table->text('motivation')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->text('latest_company_reference')->nullable();
            $table->text('second_latest_company_reference')->nullable();
            $table->text('third_latest_company_reference')->nullable();
            $table->boolean('serious_disease')->nullable();
            $table->text('serious_disease_details')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'current_age', 'marital_status', 'current_status', 'current_domicile',
                'english_fluency', 'foreign_language_fluency', 'current_salary_answer',
                'additional_benefits', 'expected_salary_answer', 'motivation',
                'reason_for_leaving', 'latest_company_reference',
                'second_latest_company_reference', 'third_latest_company_reference',
                'serious_disease', 'serious_disease_details',
            ]);
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->unsignedBigInteger('expected_salary')->nullable(false)->change();
            $table->string('notice_period')->nullable(false)->change();
        });
    }
};
