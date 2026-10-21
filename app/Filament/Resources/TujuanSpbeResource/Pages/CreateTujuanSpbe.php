<?php

namespace App\Filament\Resources\TujuanSpbeResource\Pages;

use App\Filament\Resources\TujuanSpbeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTujuanSpbe extends CreateRecord
{
    protected static string $resource = TujuanSpbeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
