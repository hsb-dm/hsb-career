<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'area_of_interest', 'cv_path', 'status'])]
class TalentPoolEntry extends Model
{
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
