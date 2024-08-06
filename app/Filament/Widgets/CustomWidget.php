<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendeeResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class CustomWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.custom-widget';

    public function callNotification(): Action
    {
        return Action::make('callNotification')
            ->button()
            ->color('info')
            ->label('Send notification')
            ->action(function () {
                Notification::make()->info()
                    ->title('Notification sent!')
                    ->body('Notification sent successfully!')
                    ->duration(2000)
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('undo')
                            ->link()
                            ->color('gray'),
                        \Filament\Notifications\Actions\Action::make('goToAttendees')
                            ->button()
                            ->color('info')
                            ->url(AttendeeResource::getUrl('edit', ['record' => 2])),
                    ])
                    ->send();
            });
    }
}
