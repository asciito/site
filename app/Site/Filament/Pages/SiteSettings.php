<?php

namespace App\Site\Filament\Pages;

use App\Filament\Pages\SettingsPage as Page;
use App\Site\Settings\SiteSettings as Settings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class SiteSettings extends Page
{
    protected static string $settingsClass = Settings::class;

    protected static bool $shouldRegisterNavigation = false;

    public function getSettingsFields(): array
    {
        return [
            Section::make(__('Site Configuration'))
                ->description('Configure the basic settings for your site.')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(32),
                    Textarea::make('description')
                        ->required()
                        ->maxLength(120),
                    // TODO: Add logo, media and favicon uploaders
                    Group::make([
                        TextInput::make('twitter_handler')
                            ->label('X/Twitter')
                            ->required()
                            ->prefixIcon('fab-x-twitter'),
                        TextInput::make('facebook_handler')
                            ->label('Facebook')
                            ->required()
                            ->prefixIcon('fab-facebook-f'),
                        TextInput::make('instagram_handler')
                            ->label('Instagram')
                            ->required()
                            ->prefixIcon('fab-instagram'),
                        TextInput::make('linkedin_handler')
                            ->label('LinkedIn')
                            ->required()
                            ->prefixIcon('fab-linkedin'),
                        TextInput::make('github_handler')
                            ->label('Github')
                            ->required()
                            ->prefixIcon('fab-github'),
                    ]),
                ])
                ->aside()
                ->columnSpanFull(),
        ];
    }
}
