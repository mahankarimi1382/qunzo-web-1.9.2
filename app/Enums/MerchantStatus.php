<?php

namespace App\Enums;

enum MerchantStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Disabled = 'disabled';
}
