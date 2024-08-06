<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public static function getForm(): array
    {

        return [
            Section::make('Conference Details')
                ->columns(2)
                ->description('Provide some basic information about the conference')
                ->schema([
                    TextInput::make('name')
                        ->label('Conference')
                        ->helperText('Name of the Conference')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(50),
                    RichEditor::make('description')
                        ->columnSpanFull()
                        ->required(),
                    DateTimePicker::make('start_date')
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->required(),
                    Fieldset::make('Conference Status')
                        ->columns(1)
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'rejected' => 'Rejected',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                            Toggle::make('is_published')
                                ->default(true),
                        ]),
                ]),
            Section::make('Location')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class)
                        ->required(),
                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->editOptionForm(Venue::getForm())
                        ->createOptionForm(Venue::getForm())
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            return $query->where('region', $get('region'));
                        }),
                ]),
            Fieldset::make('Speakers List')
                ->schema([
                    CheckboxList::make('speakers')
                        ->relationship('speakers', 'name')
                        ->options(
                            Speaker::all()->pluck('name', 'id')
                        )
                        ->columns(4)
                        ->columnSpanFull()
                        ->searchable()
                        ->required(),
                ]),
            Actions::make([
                Action::make('star')
                    ->label('Fill with Factory Data')
                    ->icon('heroicon-m-star')
                    ->color('info')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }

                        if (!app()->environment('local')) {
                            return false;
                        }

                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();

                        $livewire->form->fill($data);
                    }),
            ]),
        ];
    }
}
