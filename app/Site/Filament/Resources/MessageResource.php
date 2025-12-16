<?php

namespace App\Site\Filament\Resources;

use App\MessageStatusEnum;
use App\Site\Models\Contact;
use App\Site\Models\Message;
use BackedEnum;
use Filament\Actions;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 100;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Flex::make([
                    Schemas\Components\Section::make([
                        Infolists\Components\TextEntry::make('contact')
                            ->formatStateUsing(fn (Contact $state) => "{$state->email} ({$state->name})")
                            ->label('From')
                            ->url(fn (Contact $state) => "mailto:{$state->email}"),
                        Infolists\Components\TextEntry::make('message')
                            ->words(50),
                    ]),
                    Schemas\Components\Section::make([
                        Infolists\Components\TextEntry::make('status')
                            ->formatStateUsing(fn (MessageStatusEnum $state) => $state->name)
                            ->icon(fn (MessageStatusEnum $state) => match ($state) {
                                MessageStatusEnum::READ => 'heroicon-s-eye',
                                MessageStatusEnum::UNREAD => 'heroicon-s-eye-slash',
                            })
                            ->iconColor(fn (MessageStatusEnum $state) => match ($state) {
                                MessageStatusEnum::READ => Color::Green,
                                MessageStatusEnum::UNREAD => Color::Red
                            })
                            ->color(fn (MessageStatusEnum $state) => match ($state) {
                                MessageStatusEnum::READ => Color::Green,
                                MessageStatusEnum::UNREAD => Color::Red
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->date('F d, Y \a\t H:i'),
                    ])
                        ->grow(false),
                ])
                    ->from('md')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name'),
                Tables\Columns\TextColumn::make('message')
                    ->wrap()
                    ->limit(80),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('F d, Y')
                    ->label('Received at'),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (MessageStatusEnum $state) => $state->name)
                    ->icon(fn (MessageStatusEnum $state) => match ($state) {
                        MessageStatusEnum::READ => 'heroicon-s-eye',
                        MessageStatusEnum::UNREAD => 'heroicon-s-eye-slash',
                    })
                    ->iconColor(fn (MessageStatusEnum $state) => match ($state) {
                        MessageStatusEnum::READ => Color::Green,
                        MessageStatusEnum::UNREAD => Color::Red
                    })
                    ->color(fn (MessageStatusEnum $state) => match ($state) {
                        MessageStatusEnum::READ => Color::Green,
                        MessageStatusEnum::UNREAD => Color::Red
                    }),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => \App\Site\Filament\Resources\MessageResource\Pages\ListMessages::route('/'),
            'view' => \App\Site\Filament\Resources\MessageResource\Pages\ViewMessage::route('/{record}'),
        ];
    }
}
