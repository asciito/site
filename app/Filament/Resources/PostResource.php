<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Scopes\ModelStatusScope;
use App\Site\Enums\Status;
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
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, string $operation, string $state) {
                                        if ($operation === 'create' && ! $get('edited-manually')) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('slug')
                                    ->alphaDash()
                                    ->unique(ignoreRecord: true)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, string $operation, ?Post $record, ?string $state) {
                                        if ($operation !== 'create' && $record?->isPublished()) {
                                            if ($state !== Str::slug($get('title'))) {
                                                $set('edited-manually', true);
                                            } else {
                                                $set('edited-manually', false);
                                            }
                                        }
                                    })
                                    ->disabled(fn (?Post $record) => $record?->isPublished())
                                    ->required(),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
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
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                \App\Site\Filament\Tables\Filters\StatusFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
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
