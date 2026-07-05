<?php

namespace App\Enums;

enum NavigationType: string
{
    case Both = 'Both';
    case Header = 'Header';
    case Footer = 'Footer';
    case WidgetOne = 'Widget One';
    case WidgetTwo = 'Widget Two';
    case WidgetThree = 'Widget Three';
}
