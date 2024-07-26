<?php

namespace App\Site\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class SiteSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = \App\Site\SiteSettings::class;

    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->description('General site options')
                    ->aside()
                    ->schema([
                        TextInput::make('site_name'),
                        Textarea::make('site_description'),
                        FileUpload::make('site_image'),
                    ]),
            ]);
    }
}
