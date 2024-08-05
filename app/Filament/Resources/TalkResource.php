<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(60);
                    }),
                ImageColumn::make('speaker.avatar')
                    ->label('Speaker avatar')
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name='.urlencode($record->speaker->name);
                    })
                    ->circular(),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('new_talk')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                IconColumn::make('length')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
