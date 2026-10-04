<?php

namespace App\Models;

use App\Enums\ApplicantStatus;
use Database\Factories\ApplicantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $job_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int|null $current_salary
 * @property int $expected_salary
 * @property string $notice_period
 * @property string|null $cv_path
 * @property ApplicantStatus $status
 * @property string|null $hr_note
 * @property Carbon $applied_at
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
])]
class Applicant extends Model
{
    /** @use HasFactory<ApplicantFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Job, $this>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function salaryIncrease(): ?int
    {
        if ($this->current_salary === null) {
            return null;
        }

        return $this->expected_salary - $this->current_salary;
    }

    public function salaryIncreasePercentage(): ?float
    {
        if ($this->current_salary === null || $this->current_salary <= 0) {
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
        ];
    }
}
