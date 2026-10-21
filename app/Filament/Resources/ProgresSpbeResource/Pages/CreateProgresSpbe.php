<?php

namespace App\Filament\Resources\ProgresSpbeResource\Pages;

use App\Filament\Resources\ProgresSpbeResource;
use App\Filament\Traits\LogsActivity;
use Filament\Resources\Pages\CreateRecord;

class CreateProgresSpbe extends CreateRecord
{
    use LogsActivity;

    protected static string $resource = ProgresSpbeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
