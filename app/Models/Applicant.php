<?php

namespace App\Models;

use App\Enums\ApplicantStatus;
use App\Mail\ApplicantRejected;
use App\Support\CvStorage;
use Database\Factories\ApplicantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * @property int $id
 * @property int $job_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property Carbon|null $date_of_birth
 * @property int|null $current_salary
 * @property int|null $expected_salary
 * @property string|null $notice_period
 * @property string|null $cv_path
 * @property ApplicantStatus $status
 * @property string|null $hr_note
 * @property Carbon $applied_at
 * @property Carbon|null $rejection_email_sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Job $job
 */
#[Fillable([
    'job_id',
    'name',
    'email',
    'phone',
    'current_salary',
    'expected_salary',
    'notice_period',
    'cv_path',
    'status',
    'hr_note',
    'applied_at',
    'current_age',
    'date_of_birth',
    'marital_status',
    'current_status',
    'current_domicile',
    'english_fluency',
    'foreign_language_fluency',
    'current_salary_answer',
    'additional_benefits',
    'expected_salary_answer',
    'motivation',
    'reason_for_leaving',
    'latest_company_reference',
    'second_latest_company_reference',
    'third_latest_company_reference',
    'serious_disease',
    'serious_disease_details',
    'application_source',
    'referral_from',
])]
class Applicant extends Model
{
    /** @use HasFactory<ApplicantFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleted(function (self $applicant): void {
            if ($disk = CvStorage::readDisk($applicant->cv_path)) {
                Storage::disk($disk)->delete($applicant->cv_path);
            }
        });
    }

    /**
     * @return BelongsTo<Job, $this>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function sendRejectionEmail(): bool
    {
        return DB::transaction(function (): bool {
            $applicant = static::query()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            if ($applicant->status !== ApplicantStatus::Rejected || blank($applicant->email) || $applicant->rejection_email_sent_at !== null) {
                return false;
            }

            if (app()->isProduction() && in_array(config('mail.default'), ['log', 'array', 'failover'], true)) {
                throw new RuntimeException('Configure a delivery mailer before sending rejection emails.');
            }

            Mail::to($applicant->email)->send(new ApplicantRejected($applicant->name, $applicant->job->title));
            $applicant->forceFill(['rejection_email_sent_at' => now()])->saveQuietly();
            $this->rejection_email_sent_at = $applicant->rejection_email_sent_at;

            return true;
        });
    }

    public function salaryIncrease(): ?int
    {
        if ($this->current_salary === null || $this->expected_salary === null) {
            return null;
        }

        return $this->expected_salary - $this->current_salary;
    }

    public function salaryIncreasePercentage(): ?float
    {
        if ($this->current_salary === null || $this->current_salary <= 0 || $this->expected_salary === null) {
            return null;
        }

        return (($this->expected_salary - $this->current_salary) / $this->current_salary) * 100;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_salary' => 'integer',
            'expected_salary' => 'integer',
            'status' => ApplicantStatus::class,
            'applied_at' => 'datetime',
            'rejection_email_sent_at' => 'datetime',
            'current_age' => 'integer',
            'date_of_birth' => 'date',
            'serious_disease' => 'boolean',
        ];
    }
}
