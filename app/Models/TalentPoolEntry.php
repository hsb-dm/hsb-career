<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'area_of_interest', 'cv_path'])]
class TalentPoolEntry extends Model
{
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
