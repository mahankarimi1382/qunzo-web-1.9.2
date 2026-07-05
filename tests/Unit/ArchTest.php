<?php

arch()
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r', 'ray']);

arch()
    ->expect('App\Traits')
    ->toBeTraits();
