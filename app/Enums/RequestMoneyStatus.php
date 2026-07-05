<?php

namespace App\Enums;

enum RequestMoneyStatus: string
{
    case Success = 'success';
    case Pending = 'pending';
    case Rejected = 'rejected';
}
