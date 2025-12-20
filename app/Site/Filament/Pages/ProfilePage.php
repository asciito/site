<?php

namespace App\Site\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Override;

class ProfilePage extends EditProfile
{
    #[Override]
    public static function isSimple(): bool
    {
        return false;
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                        $this->getResumeFormComponent(),
                        $this->getIntroductionComponent(),
                        $this->getDescriptionFormComponent(),
                    ])
                    ->aside()
                    ->description('Update the your profile information and provide the needed information.'),
            ]);
    }

    #[Override]
    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }

    protected function getDescriptionFormComponent(): Component
    {
        return RichEditor::make('description')
            ->label(__('Description'))
            ->toolbarButtons([
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
            ])
            ->grow()
            ->helperText('This text will be display in the `About me` section of the home page');
    }

    protected function getIntroductionComponent(): Component
    {
        return RichEditor::make('introduction')
            ->label(__('Introduction'))
            ->toolbarButtons([
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h1',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
            ])
            ->grow()
            ->helperText('This text will be display at the beginning of the home page');
    }

    protected function getResumeFormComponent(): Component
    {
        return FileUpload::make('resume')
            ->label(__('Resume'))
            ->acceptedFileTypes(['application/pdf'])
            ->formatStateUsing(fn () => Storage::exists('resume.pdf') ? ['resume.pdf'] : [])
            ->saveUploadedFileUsing(function (Component $component, TemporaryUploadedFile $file) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }

                Storage::putFileAs('/', $file, $filename);

                return $file;
            })
            ->deleteUploadedFileUsing(function (Component $component) {
                $filename = 'resume.pdf';

                if (Storage::exists($filename)) {
                    Storage::delete($filename);
                }
            })
            ->panelLayout(null);
    }
}
