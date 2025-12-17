<?php

namespace App\Site\Filament\Resources\Messages\Pages;

use App\Site\Filament\Resources\Messages\MessageResource;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;
}
