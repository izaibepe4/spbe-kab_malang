<?php

namespace App\Filament\Resources\PaparanItemResource\Pages;

use App\Filament\Resources\PaparanItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaparanItem extends CreateRecord
{
    protected static string $resource = PaparanItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
