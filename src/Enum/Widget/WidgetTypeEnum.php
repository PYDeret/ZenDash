<?php

declare(strict_types=1);

namespace App\Enum\Widget;

enum WidgetTypeEnum: string
{
    case NOTE = 'note';
    case TODO = 'todo';
    case WEATHER = 'weather';
}
