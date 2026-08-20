<?php

declare(strict_types=1);

namespace App\Enums;

enum Audience: string
{
    case Recruits = 'recruits';
    case Students = 'students';
    case Alumni = 'alumni';
    case TeachingAssistants = 'teaching_assistants';
    case Teachers = 'teachers';
    case Administrators = 'administrators';
}
