<?php

namespace App;

enum MessageStatusEnum: string
{
    case READ = 'read';

    case UNREAD = 'unread';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
