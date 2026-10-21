<?php

namespace App\Filament\Resources\UserAdminResource\Pages;

use App\Filament\Resources\UserAdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserAdmin extends CreateRecord
{
    protected static string $resource = UserAdminResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User Admin berhasil dibuat';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
