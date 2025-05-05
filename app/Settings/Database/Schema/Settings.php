<?php

declare(strict_types=1);

namespace App\Settings\Database\Schema;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'settings.schema';
    }
}
