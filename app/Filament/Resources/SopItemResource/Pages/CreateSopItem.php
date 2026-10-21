<?php

namespace App\Filament\Resources\SopItemResource\Pages;

use App\Filament\Resources\SopItemResource;
use App\Filament\Traits\LogsActivity;
use Filament\Resources\Pages\CreateRecord;

class CreateSopItem extends CreateRecord
{
    use LogsActivity;

    protected static string $resource = SopItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
