<?php

namespace App\Filament\Resources\AgendaImageResource\Pages;

use App\Filament\Resources\AgendaImageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgendaImage extends CreateRecord
{
    protected static string $resource = AgendaImageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
