<?php

namespace App\Site\Enums;

use Illuminate\Support\Collection;

enum Status: string
{
    case DRAFT = 'draft';

    case PUBLISHED = 'published';

    case ARCHIVED = 'archived';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function asOptions(): Collection
    {
        return collect(self::cases())->mapWithKeys(fn (self $status) => [$status->value => $status->name]);
    }
}
