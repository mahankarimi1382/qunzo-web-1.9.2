<?php

namespace App\Enums;

enum BoardingStep: string
{
    case EMAIL_VERIFICATION = 'email_verification';
    case PASSWORD_SETUP = 'password_setup';
    case PERSONAL_INFO = 'personal_info';
    case ID_VERIFICATION = 'id_verification';
    case COMPLETED = 'completed';
}
