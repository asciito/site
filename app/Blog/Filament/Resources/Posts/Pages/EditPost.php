<?php

namespace App\Blog\Filament\Resources\Posts\Pages;

use App\Blog\Filament\Resources\Posts\PostResource;
use App\Blog\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Override;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->visible(fn (Post $record) => $record->isDraft())
                ->action(function (Action $action, Post $record) {
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
            Action::make('view')
                ->hidden(fn (Post $record) => $record->isArchived())
                ->url(fn (Post $record) => route('post', $record), true)
                ->color('gray'),
            ActionGroup::make([
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ]),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            'editing' => $data['slug'] !== Str::slug($data['title']),
        ];
    }
}
