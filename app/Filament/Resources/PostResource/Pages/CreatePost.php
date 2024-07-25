<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Site\Enums\Status;
use Filament\Actions;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('draft')
                ->label('Save as Draft')
                ->link()
                ->mutateFormDataUsing(fn (array $data) => [
                    ...$data,
                    $data['status'] => Status::DRAFT
                ])
                ->color('gray')
                ->action('create'),
            Actions\Action::make('publish')
                ->requiresConfirmation(),
        ];
    }
}
