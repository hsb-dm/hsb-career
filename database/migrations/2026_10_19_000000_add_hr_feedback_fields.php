<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table): void {
            $table->string('application_source')->nullable();
            $table->string('referral_from')->nullable();
        });

        Schema::table('talent_pool_entries', function (Blueprint $table): void {
            $table->string('status')->nullable();
        });

        DB::table('applicants')->select('id', 'current_salary', 'expected_salary', 'current_salary_answer', 'expected_salary_answer')
            ->orderBy('id')->chunkById(200, function ($applicants): void {
                foreach ($applicants as $applicant) {
                    $values = [];

                    foreach (['current', 'expected'] as $salary) {
                        if ($applicant->{$salary.'_salary'} !== null) {
                            continue;
                        }

                        $answer = trim((string) $applicant->{$salary.'_salary_answer'});
                        if (preg_match('/^(?:Rp\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,15})(?:\s|$)/i', $answer, $matches)) {
                            $values[$salary.'_salary'] = (int) str_replace('.', '', $matches[1]);
                        }
                    }

                    if ($values !== []) {
                        DB::table('applicants')->where('id', $applicant->id)->update($values);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('applicants', fn (Blueprint $table) => $table->dropColumn(['application_source', 'referral_from']));
        Schema::table('talent_pool_entries', fn (Blueprint $table) => $table->dropColumn('status'));
    }
};
