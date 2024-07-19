<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use function Livewire\Volt\dehydrate;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

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
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, string $operation, ?string $state) {
                                        if ($operation !== 'create') {
                                            if ($state !== Str::slug($get('title'))) {
                                                $set('edited-manually', true);
                                            } else {
                                                $set('edited-manually', false);
                                            }
                                        }
                                    })
                                    ->required(),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'md' => 9,
                                'lg' => 8
                            ]),
                        Forms\Components\Section::make()
                            ->schema([

                            ])
                            ->columnSpan([
                                'md' => 3,
                                'lg' => 4,
                            ]),
                    ])
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last time updated')
                    ->formatStateUsing(fn (Carbon $state) => $state->diffForHumans())
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
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
            ]);
    }
}
