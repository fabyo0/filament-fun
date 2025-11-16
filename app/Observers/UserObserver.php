<?php

namespace App\Observers;

use App\Events\NewNotificationEvent;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class UserObserver
{
    public function created(User $user): void
    {
        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        if (app()->runningInConsole() && $admins->count() === 1 && $user->hasRole('admin')) {
            Notification::make()
                ->title('Welcome Admin!')
                ->body("Your admin account has been created successfully. Welcome to the system!")
                ->icon('heroicon-o-shield-check')
                ->success()
                ->sendToDatabase($user);
            return;
        }

        if (app()->runningInConsole()) {
            return;
        }

        $recipients = $admins->where('id', '!=', $user->id);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('New User Created')
            ->body("User {$user->name} has been created successfully")
            ->icon('heroicon-o-user-plus')
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(UserResource::getUrl('view', ['record' => $user])),
            ])
            ->sendToDatabase($recipients);
    }

    public function deleted(User $user): void
    {

        if (app()->runningInConsole()) {
            return;
        }

        $admins = User::role('admin')
            ->where('id', '!=', $user->id)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('User Deleted')
            ->body("User {$user->name} has been removed from the system")
            ->icon('heroicon-o-user-minus')
            ->danger()
            ->persistent()
            ->sendToDatabase($admins);
    }
}
