<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Pages\SettingsPage as Page;
use App\Settings\SiteSettings as Settings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                    Group::make([
                        FileUpload::make('image')
                            ->label(__('SEO image'))
                            ->validationAttribute('image')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/webp', 'image/jpeg', 'image/png'])
                            ->imageEditorAspectRatios([
                                null,
                                '1.91:1',
                            ])
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('630')
                            ->imageCropAspectRatio('1.91:1')
                            ->panelLayout(null)
                            ->previewable(false)
                            ->rules([
                                Rule::dimensions()
                                    ->ratio(1.91)
                                    ->minWidth(1200)
                                    ->maxWidth(1920),
                            ])
                            ->hintIcon(Heroicon::InformationCircle)
                            ->hintColor(Color::Zinc)
                            ->hintIconTooltip('Width (min: 1200px - max: 1920px) - Ration (1.91:1)')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                $host = parse_url((string) config('app.url'), PHP_URL_HOST);

                                $ext = $file->guessExtension();

                                return Str::before($host, '.').'-open-graph.'.$ext;
                            }),
                        FileUpload::make('favicon')
                            ->label(__('Favicon'))
                            ->validationAttribute('favicon')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->imageCropAspectRatio('1:1')
                            ->panelLayout(null)
                            ->previewable(false)
                            ->rules([
                                Rule::dimensions()
                                    ->ratio(1)
                                    ->minWidth(16)
                                    ->maxWidth(512),
                                'mimes:ico,png',
                            ])
                            ->hintIcon(Heroicon::InformationCircle)
                            ->hintColor(Color::Zinc)
                            ->hintIconTooltip('Size (min: 16px  - max: 512px) - Ration (1:1)')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) {
                                $ext = $file->guessExtension();

                                return 'favicon.'.$ext;
                            }),
                    ])->columns([
                        'default' => 1,
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                    ]),
                ])
                ->aside()
                ->columnSpanFull(),
            Section::make(__('Social Media'))
                ->description('Information related to the available social media configuration')
                ->schema([
                    Section::make(__('Handlers'))
                        ->schema([
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
                        ->collapsed()
                        ->collapsible(),
                ])
                ->aside()
                ->columnSpanFull(),
        ];
    }
}
