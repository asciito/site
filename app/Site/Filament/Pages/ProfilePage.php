<?php

namespace App\Site\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends EditProfile
{
    public static function isSimple(): bool
    {
        return false;
    }

    protected function getForms(): array
    {
        $form = parent::getForms()['form'];

        return [
            'form' => $form->schema([
                Forms\Components\Section::make('Profile Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getResumeFormComponent(),
                        $this->getDescriptionFormComponent(),
                    ])
                    ->aside(),
            ])
                ->inlineLabel(false),
        ];
    }

    protected function getDescriptionFormComponent(): \Filament\Forms\Components\Component
    {
        return \Filament\Forms\Components\MarkdownEditor::make('description')
            ->label(__('Description'))
            ->toolbarButtons([
                'blockquote',
                'bold',
                'bulletList',
                'italic',
                'link',
                'heading',
                'orderedList',
                'redo',
                'strike',
                'undo',
            ])
            ->grow();
    }

    protected function getResumeFormComponent(): \Filament\Forms\Components\Component
    {
        return \Filament\Forms\Components\FileUpload::make('resume')
            ->label(__('Resume'))
            ->acceptedFileTypes(['application/pdf'])
            ->formatStateUsing(fn () => Storage::exists('resume.pdf') ? ['resume.pdf'] : [])
            ->saveUploadedFileUsing(function (Forms\Components\Component $component, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }

                Storage::putFileAs('/', $file, $filename);

                return $file;
            })
            ->deleteUploadedFileUsing(function (Forms\Components\Component $component) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }
            });
    }
}
