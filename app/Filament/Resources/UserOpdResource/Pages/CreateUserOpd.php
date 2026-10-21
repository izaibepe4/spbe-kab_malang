<?php

namespace App\Filament\Resources\UserOpdResource\Pages;

use App\Filament\Resources\UserOpdResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserOpd extends CreateRecord
{
    protected static string $resource = UserOpdResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User OPD berhasil dibuat';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
