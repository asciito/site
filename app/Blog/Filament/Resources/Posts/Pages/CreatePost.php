<?php

namespace App\Blog\Filament\Resources\Posts\Pages;

use App\Blog\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('draft')
                ->label('Save as Draft')
                ->link()
                ->color('gray')
                ->action(function (Action $action) {
                    try {
                        $this->create();

                        $this->getRecord()->draft();
                    } catch (ValidationException $e) {
                        $this->setErrorBag($e->validator->errors());

                        $action->cancel();
                    }
                })
                ->keyBindings(['mod+s']),
            Action::make('publish')
                ->requiresConfirmation()
                ->action(function (Action $action) {
                    try {
                        $this->create();

                        $this->getRecord()->publish();
                    } catch (ValidationException $e) {
                        $this->setErrorBag($e->validator->errors());

                        $action->cancel();
                    }
                })
                ->keyBindings(['mod+p']),
        ];
    }
}
