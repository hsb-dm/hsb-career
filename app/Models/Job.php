<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $department
 * @property string $location
 * @property EmploymentType $employment_type
 * @property WorkArrangement $work_arrangement
 * @property string $description
 * @property JobStatus $status
 * @property Carbon|null $published_at
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'title',
    'slug',
    'department',
    'location',
    'employment_type',
    'work_arrangement',
    'description',
    'status',
    'published_at',
    'closed_at',
])]
class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    /** @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', JobStatus::Active);
    }

    protected static function booted(): void
    {
        static::saving(function (Job $job): void {
            if (blank($job->slug) && filled($job->title)) {
                $job->slug = static::uniqueSlug($job->title, $job->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $baseSlug = filled($baseSlug) ? $baseSlug : 'job';
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * @return HasMany<Applicant, $this>
     */
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'work_arrangement' => WorkArrangement::class,
            'status' => JobStatus::class,
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
