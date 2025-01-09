<?php

namespace App\Observers;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('New Employee Added')
            ->body("Name: {$employee->first_name} {$employee->last_name}\nPosition: {$employee->position}")
            ->icon('heroicon-o-user-group')
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(EmployeeResource::getUrl('view', ['record' => $employee])),
            ])
            ->duration(5000)
            ->sendToDatabase($recipient);
    }

    public function deleted(Employee $employee): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('Employee Removed')
            ->body("Employee {$employee->first_name} {$employee->last_name} has been removed")
            ->icon('heroicon-o-user-minus')
            ->danger()
            ->persistent()
            ->sendToDatabase($recipient);
    }
}
