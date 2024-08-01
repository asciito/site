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
                Section::make('Social Media Links')
                    ->description('Manage your social media links for the site')
                    ->aside()
                    ->schema([
                        TextInput::make('instagram_handler')
                            ->label('Instagram')
                            ->suffixIcon('heroicon-s-globe-alt')
                            ->prefix('instagram/'),
                        TextInput::make('linkedin_handler')
                            ->label('Linked In')
                            ->suffixIcon('heroicon-s-globe-alt')
                            ->prefix('linkedin.com/in/'),
                        TextInput::make('twitter_handler')
                            ->label('X/Twitter')
                            ->suffixIcon('heroicon-s-globe-alt')
                            ->prefix('x.com/'),
                        TextInput::make('github_handler')
                            ->label('GitHub')
                            ->suffixIcon('heroicon-s-globe-alt')
                            ->prefix('github.com/'),
                    ]),
            ]);
    }
}
