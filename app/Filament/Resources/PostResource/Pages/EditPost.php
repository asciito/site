<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Site\Enums\Status;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->visible(fn (Post $record) => $record->isDraft())
                ->action(function (Post $record) {
                    $record->update(['status' => Status::PUBLISHED]);

                    $this->getSavedNotification()->send();
                })
                ->requiresConfirmation(),
            Actions\Action::make('view')
                ->url(fn (Post $record) => route('post', $record), true)
                ->color('gray'),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ]),
        ];
    }
}
