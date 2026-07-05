<?php

namespace App\Enums;

enum TxnStatus: string
{
    case Success = 'Success';
    case Pending = 'Pending';
    case Failed = 'Failed';
}
