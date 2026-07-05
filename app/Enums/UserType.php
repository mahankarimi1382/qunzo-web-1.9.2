<?php

namespace App\Enums;

enum UserType: string
{
    case User = 'User';
    case Agent = 'Agent';
    case Merchant = 'Merchant';
}
