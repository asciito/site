<?php

namespace App\Site\Filament\Resources\MessageResource\Pages;

use App\MessageStatusEnum;
use App\Site\Filament\Resources\MessageResource;
use App\Site\Models\Message;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Mark as read')
                ->hidden(fn (Message $record) => $record->status === MessageStatusEnum::READ)
                ->action(fn (Message $record) => $record->markAsRead())
                ->requiresConfirmation(),
        ];
    }
}
