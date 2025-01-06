<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $subheading = 'This form will create user';

    protected function handleRecordCreation(array $data): Model
    {
        $role = $data['roles'] ?? null;
        unset($data['roles']);

        $user = static::getModel()::create($data);

        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }
}
