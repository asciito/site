<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\MessageStatusEnum;
use App\Models\Message;
use App\Models\Contact;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 100;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Split::make([
                    Infolists\Components\Section::make([
                        Infolists\Components\TextEntry::make('contact')
                            ->formatStateUsing(fn (Contact $state) => "{$state->email} ({$state->name})")
                            ->label('From')
                            ->url(fn (Contact $state) => "mailto:{$state->email}"),
                        Infolists\Components\TextEntry::make('message')
                            ->words(50),
                    ])
                        ->columnSpan(8),
                    Infolists\Components\Section::make([
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMessages::route('/'),
            'view' => Pages\ViewMessage::route('/{record}'),
        ];
    }
}
