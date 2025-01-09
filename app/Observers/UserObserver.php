<?php

namespace App\Observers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class UserObserver
{
    public function created(User $user): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('New User Created')
            ->body("User {$user->name} has been created successfully")
            ->icon('heroicon-o-user-plus')
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(UserResource::getUrl('view', ['record' => $user])),
            ])
            ->duration(5000)
            ->sendToDatabase($recipient);
    }

    public function deleted(User $user): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('User Deleted')
            ->body("User {$user->name} has been removed from the system")
            ->icon('heroicon-o-user-minus')
            ->danger()
            ->persistent()
            ->sendToDatabase($recipient);
    }
}
