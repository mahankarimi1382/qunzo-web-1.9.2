<?php

namespace App\Enums;

enum MegamenuType: int
{
    case ListWithPreview = 1;
    case Grid = 2;

    public function isListWithPreview(): bool
    {
        return $this->value === self::ListWithPreview->value;
    }

    public function isGrid(): bool
    {
        return $this->value === self::Grid->value;
    }
}
