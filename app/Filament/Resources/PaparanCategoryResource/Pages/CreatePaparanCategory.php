<?php

namespace App\Filament\Resources\PaparanCategoryResource\Pages;

use App\Filament\Resources\PaparanCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaparanCategory extends CreateRecord
{
    protected static string $resource = PaparanCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
