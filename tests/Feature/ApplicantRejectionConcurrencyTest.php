<?php

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Database\QueryException;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

test('a second PostgreSQL connection cannot send while the first holds the applicant row', function () {
    if (DB::getDriverName() !== 'pgsql' || ! str_ends_with((string) config('database.connections.pgsql.database'), '_test')) {
        $this->markTestSkipped('Requires an isolated PostgreSQL database ending in _test.');
    }

    Artisan::call('migrate', ['--force' => true]);
    $job = Job::factory()->create();
    $applicant = Applicant::factory()->create([
        'job_id' => $job->id,
        'status' => ApplicantStatus::Rejected,
        'cv_path' => null,
    ]);
    config()->set('database.connections.mail_lock_probe', config('database.connections.pgsql'));
    DB::purge('mail_lock_probe');
    $blocked = false;

    Event::listen(MessageSending::class, function () use ($applicant, &$blocked): void {
        try {
            DB::connection('mail_lock_probe')->transaction(fn () => DB::connection('mail_lock_probe')
                ->table('applicants')
                ->where('id', $applicant->id)
                ->lock('FOR UPDATE NOWAIT')
                ->first());
        } catch (QueryException $exception) {
            $blocked = $exception->getCode() === '55P03';
        }
    });

    try {
        expect($applicant->sendRejectionEmail())->toBeTrue();
        expect($blocked)->toBeTrue();
    } finally {
        DB::disconnect('mail_lock_probe');
        $applicant->delete();
        $job->delete();
    }
});
