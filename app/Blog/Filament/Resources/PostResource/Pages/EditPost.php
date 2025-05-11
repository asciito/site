<?php

namespace App\Blog\Filament\Resources\PostResource\Pages;

use App\Blog\Filament\Resources\PostResource;
use App\Blog\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->visible(fn (Post $record) => $record->isDraft())
                ->action(function (Actions\Action $action, Post $record) {
                    try {
                        $this->save(false);

                        $record->publish();
                    } catch (ValidationException $e) {
                        $this->setErrorBag($e->validator->errors());

                        $action->close();
                    }
                })
                ->requiresConfirmation()
                ->keyBindings(['mod+p']),
            Actions\Action::make('view')
                ->hidden(fn (Post $record) => $record->isArchived())
                ->url(fn (Post $record) => route('post', $record), true)
                ->color('gray'),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ]),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            'editing' => $data['slug'] !== \Illuminate\Support\Str::slug($data['title']),
        ];
    }
}
