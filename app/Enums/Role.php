<?php

namespace App\Enums;

enum Role: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
}
