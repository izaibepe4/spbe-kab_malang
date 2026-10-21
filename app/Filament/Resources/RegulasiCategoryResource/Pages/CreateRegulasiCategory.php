<?php

namespace App\Filament\Resources\RegulasiCategoryResource\Pages;

use App\Filament\Resources\RegulasiCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegulasiCategory extends CreateRecord
{
    protected static string $resource = RegulasiCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
