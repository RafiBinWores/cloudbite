<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Manager = 'manager';
    case Admin = 'admin';
}
