<?php

namespace App\Filament\Resources;

use App\Enums\Region;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use App\Models\Speaker;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Conference')
                    ->helperText('Name of the Conference')
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(50),
                Forms\Components\RichEditor::make('description')
                    ->columnSpan(2)
                    ->required(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                        'archived' => 'Archived',
                    ])
                    ->required(),
                Forms\Components\Select::make('region')
                    ->live()
                    ->enum(Region::class)
                    ->options(Region::class)
                    ->required(),
                Forms\Components\Select::make('venue_id')
                    ->searchable()
                    ->preload()
                    ->editOptionForm(Venue::getForm())
                    ->createOptionForm(Venue::getForm())
                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                        return $query->where('region', $get('region'));
                    }),
                Forms\Components\CheckboxList::make('speakers')
                    ->relationship('speakers', 'name')
                    ->options(
                        Speaker::all()->pluck('name', 'id')
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
