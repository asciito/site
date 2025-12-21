<?php

namespace App\Blog\Filament\Resources\Posts;

use App\Blog\Enums\Status;
use App\Blog\Filament\Resources\Posts\Pages\CreatePost;
use App\Blog\Filament\Resources\Posts\Pages\EditPost;
use App\Blog\Filament\Resources\Posts\Pages\ListPosts;
use App\Blog\Filament\Tables\Filters\StatusFilter;
use App\Blog\Models\Post;
use App\Models\Scopes\ModelStatusScope;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Override;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 50;

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'md' => 12,
                ])
                    ->schema([
                        Section::make([
                            Toggle::make('preview')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, Get $get) => $set('preview', $get('preview', false)))
                                ->hidden(fn (string $operation) => $operation === 'create'),
                            Group::make()
                                ->schema([
                                    TextInput::make('title')
                                        ->live(debounce: 350)
                                        ->required()
                                        ->afterStateUpdated(function (Set $set, Get $get, ?Post $record, string $operation, ?string $state) {
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
                                    TextInput::make('slug')
                                        ->live(debounce: 350)
                                        ->unique(ignoreRecord: true)
                                        ->required()
                                        ->alphaDash()
                                        ->afterStateUpdated(function (Set $set, ?string $state) {
                                            $set('slug', Str::slug($state));

                                            $set('editing', true);
                                        })
                                        ->disabled(fn (?Post $record) => $record?->isPublished()),
                                    Hidden::make('editing')
                                        ->default(false),
                                    RichEditor::make('content')
                                        ->grow()
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                                ->hidden(fn (Get $get) => $get('preview')),
                            Group::make()
                                ->schema([
                                    View::make('blog::filament.forms.fields.preview'),
                                ])
                                ->hidden(fn (Get $get) => ! $get('preview')),
                        ])
                            ->columnSpan([
                                'md' => 9,
                                'lg' => 8,
                            ]),
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->loadingIndicatorPosition('right')
                                    ->uploadButtonPosition('left')
                                    ->uploadProgressIndicatorPosition('right')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->conversion('thumb')
                                    ->rules([
                                        fn (): Closure => function (string $attribute, TemporaryUploadedFile $value, Closure $fail) {
                                            [$width, $height] = getimagesize($value->getRealPath());

                                            if ($width > 1920 || $height > 1080) {
                                                $fail('The :attribute dimensions are not valid');
                                            }
                                        },
                                    ])
                                    ->responsiveImages(),
                                Textarea::make('excerpt')
                                    ->live(debounce: 250)
                                    ->maxLength(255)
                                    ->helperText(function (Textarea $component, ?string $state): Htmlable {
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
                    ])->columnSpanFull(),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('slug')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status')
                    ->formatStateUsing(fn (Status $state) => $state->name)
                    ->badge()
                    ->color(fn (Status $state) => match ($state) {
                        Status::DRAFT => Color::Gray,
                        Status::PUBLISHED => Color::Blue,
                        Status::ARCHIVED => Color::Red,
                    }),
                TextColumn::make('updated_at')
                    ->label('Last time updated')
                    ->formatStateUsing(fn (Carbon $state) => $state->diffForHumans()),
            ])
            ->recordUrl(fn (Post $record) => $record->isArchived() ? null : route('filament.webtools.resources.posts.edit', $record))
            ->filters([
                TrashedFilter::make(),
                StatusFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->hidden(fn (Post $record) => $record->isArchived())
                        ->icon('heroicon-s-eye')
                        ->url(fn (Post $record) => route('post', $record), true),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'DESC');
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                ModelStatusScope::class,
            ]);
    }
}
