<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RecruitmentWelcome extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.recruitment-welcome';

    protected int|string|array $columnSpan = 'full';
}
