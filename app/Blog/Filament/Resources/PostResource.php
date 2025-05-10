<?php

namespace App\Blog\Filament\Resources;

use App\Blog\Enums\Status;
use App\Blog\Filament\Resources\PostResource\Pages;
use App\Blog\Models\Post;
use App\Models\Scopes\ModelStatusScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'md' => 12,
                ])
                    ->schema([
                        Forms\Components\Section::make([
                            Forms\Components\Toggle::make('preview')
                                ->live()
                                ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => $set('preview', $get('preview', false)))
                                ->hidden(fn (string $operation) => $operation === 'create'),
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->live(debounce: 350)
                                        ->required()
                                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?Post $record, string $operation, ?string $state) {
                                            if ($record?->isPublished()) {
                                                return;
                                            }

                                            switch ($operation) {
                                                case 'create':
                                                    if (! $get('editing')) {
                                                        $set('slug', Str::slug($state));
                                                    }

                                                    return;
                                                case 'edit':
                                                    if (! $get('editing')) {
                                                        $set('slug', Str::slug($state));
                                                    } elseif (Str::startsWith(Str::slug($state), $get('slug'))) {
                                                        $set('slug', Str::slug($state));
                                                        $set('editing', false);
                                                    }

                                                    return;
                                            }
                                        }),
                                    Forms\Components\TextInput::make('slug')
                                        ->live(debounce: 350)
                                        ->unique(ignoreRecord: true)
                                        ->required()
                                        ->alphaDash()
                                        ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                            $set('slug', Str::slug($state));

                                            $set('editing', true);
                                        })
                                        ->disabled(fn (?Post $record) => $record?->isPublished()),
                                    Forms\Components\Hidden::make('editing')
                                        ->default(false),
                                    Forms\Components\MarkdownEditor::make('content')
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                                ->hidden(fn (Forms\Get $get) => $get('preview')),
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\View::make('site::forms.fields.preview'),
                                ])
                                ->hidden(fn (Forms\Get $get) => ! $get('preview')),
                        ])
                            ->columnSpan([
                                'md' => 9,
                                'lg' => 8,
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->loadingIndicatorPosition('right')
                                    ->uploadButtonPosition('left')
                                    ->uploadProgressIndicatorPosition('right')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->conversion('thumb')
                                    ->rules([
                                        fn (): \Closure => function (string $attribute, TemporaryUploadedFile $value, \Closure $fail) {
                                            [$width, $height] = getimagesize($value->getRealPath());

                                            if ($width > 1920 || $height > 1080) {
                                                $fail('The :attribute dimensions are not valid');
                                            }
                                        },
                                    ])
                                    ->responsiveImages(),
                                Forms\Components\Textarea::make('excerpt')
                                    ->live(debounce: 250)
                                    ->maxLength(255)
                                    ->helperText(function (Forms\Components\Textarea $component, ?string $state): Htmlable {
                                        $current = strlen($state ?? '');

                                        $color = $current / $component->getMaxLength() < 0.8 ? '--gray-600' : '--danger-600';

                                        return new HtmlString("Characters count: <span class='font-semibold text-custom-600' style='--c-600: var($color)'>$current of 255<span>");
                                    })
                                    ->rows(6),
                            ])
                            ->columnSpan([
                                'md' => 3,
                                'lg' => 4,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (Status $state) => $state->name)
                    ->badge()
                    ->color(fn (Status $state) => match ($state) {
                        Status::DRAFT => \Filament\Support\Colors\Color::Gray,
                        Status::PUBLISHED => \Filament\Support\Colors\Color::Blue,
                        Status::ARCHIVED => \Filament\Support\Colors\Color::Red,
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last time updated')
                    ->formatStateUsing(fn (Carbon $state) => $state->diffForHumans()),
            ])
            ->recordUrl(fn (Post $record) => ! $record->isArchived() ? route('filament.webtools.resources.posts.edit', $record) : null)
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                \App\Blog\Filament\Tables\Filters\StatusFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->hidden(fn (Post $record) => $record->isArchived())
                        ->icon('heroicon-s-eye')
                        ->url(fn (Post $record) => route('post', $record), true),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                ModelStatusScope::class,
            ]);
    }
}
