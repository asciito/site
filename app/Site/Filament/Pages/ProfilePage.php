<?php

namespace App\Site\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Auth\EditProfile;

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
                            $this->getDescriptionFormComponent(),
                        ])
                        ->aside()
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
}
