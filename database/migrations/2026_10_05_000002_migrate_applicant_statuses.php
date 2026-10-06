<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'new' => 'ai_ats_screened',
            'reviewing' => 'hr_interview',
            'interview' => 'user_interview',
            'accepted' => 'hired',
        ] as $old => $new) {
            DB::table('applicants')->where('status', $old)->update(['status' => $new]);
        }
    }

    public function down(): void
    {
        foreach ([
            'ai_ats_screened' => 'new',
            'hr_interview' => 'reviewing',
            'forwarded_to_user' => 'reviewing',
            'user_interview' => 'interview',
            'hired' => 'accepted',
            'on_hold' => 'reviewing',
            'no_show' => 'rejected',
            'withdraw_decline' => 'rejected',
            'study_case' => 'interview',
        ] as $current => $old) {
            DB::table('applicants')->where('status', $current)->update(['status' => $old]);
        }
    }
};
