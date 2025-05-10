<?php

namespace App\Blog\Filament\Resources\PostResource\Pages;

use App\Blog\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('draft')
                ->label('Save as Draft')
                ->link()
                ->color('gray')
                ->action(function (Actions\Action $action) {
                    try {
                        $this->create();

                        $this->getRecord()->draft();
                    } catch (ValidationException $e) {
                        $this->setErrorBag($e->validator->errors());

                        $action->cancel();
                    }
                }),
            Actions\Action::make('publish')
                ->requiresConfirmation()
                ->action(function (Actions\Action $action) {
                    try {
                        $this->create();

                        $this->getRecord()->publish();
                    } catch (ValidationException $e) {
                        $this->setErrorBag($e->validator->errors());

                        $action->cancel();
                    }
                })
                ->keyBindings(['mod+s']),
        ];
    }
}
