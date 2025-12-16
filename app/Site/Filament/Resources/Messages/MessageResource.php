<?php

namespace App\Site\Filament\Resources\Messages;

use App\MessageStatusEnum;
use App\Site\Filament\Resources\Messages\Pages\ListMessages;
use App\Site\Filament\Resources\Messages\Pages\ViewMessage;
use App\Site\Models\Contact;
use App\Site\Models\Message;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 100;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make([
                        TextEntry::make('contact')
                            ->formatStateUsing(fn (Contact $state) => "{$state->email} ({$state->name})")
                            ->label('From')
                            ->url(fn (Contact $state) => "mailto:{$state->email}"),
                        TextEntry::make('message')
                            ->words(50),
                    ])
                        ->columnSpan(8),
                    Section::make([
                        TextEntry::make('status')
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
                        TextEntry::make('created_at')
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
                TextColumn::make('contact.name'),
                TextColumn::make('message')
                    ->wrap()
                    ->limit(80),
                TextColumn::make('created_at')
                    ->date('F d, Y')
                    ->label('Received at'),
                TextColumn::make('status')
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListMessages::route('/'),
            'view' => ViewMessage::route('/{record}'),
        ];
    }
}
