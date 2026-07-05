<?php

namespace App\Enums;

enum AgentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Disabled = 'disabled';
}
