<?php

namespace App\Filament\Resources\RegulasiItemResource\Pages;

use App\Filament\Resources\RegulasiItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegulasiItem extends CreateRecord
{
    protected static string $resource = RegulasiItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
