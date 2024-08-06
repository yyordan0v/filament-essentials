<?php

namespace App\Livewire;

use App\Models\Attendee;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Livewire\Component;

class ConferenceSignUpPage extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public int $conferenceId;
    public int $price = 50000;

    public function mount()
    {
        $this->conferenceId = 1;
    }

    public function signUpAction(): Action
    {
        return Action::make('signUp')
            ->form([
                Placeholder::make('total_price')
                    ->content(function (Get $get) {
                        return '$'.count($get('attendees')) * 500;
                    }),
                Repeater::make('attendees')
                    ->schema(Attendee::getForm()),
            ])
            ->action(function ($data) {
                collect($data['attendees'])->each(function ($data) {
                    Attendee::create([
                        'conference_id' => $this->conferenceId,
                        'ticket_cost' => $this->price,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'is_paid' => true,
                    ]);
                });
            })
            ->after(function () {
                Notification::make()
                    ->success()
                    ->title('Success!')
                    ->body('You have successfully signed up for the conference.')
                    ->send();
            });
    }

    public function render()
    {
        return view('livewire.conference-sign-up-page');
    }
}
