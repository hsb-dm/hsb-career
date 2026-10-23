<?php

namespace App\Models;

use App\Support\CvStorage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'area_of_interest', 'cv_path', 'status'])]
class TalentPoolEntry extends Model
{
    protected static function booted(): void
    {
        static::deleted(function (self $entry): void {
            if ($disk = CvStorage::readDisk($entry->cv_path)) {
                Storage::disk($disk)->delete($entry->cv_path);
            }
        });
    }

    public const STATUSES = [
        'no_suitable_opportunities' => 'No suitable opportunities',
        'contacted' => 'Contacted',
    ];

    public const AREAS_OF_INTEREST = [
        'Technology',
        'Digital Marketing',
        'Operations',
        'Finance',
        'Business Development',
        'Human Resources',
        'Compliance',
    ];
}
