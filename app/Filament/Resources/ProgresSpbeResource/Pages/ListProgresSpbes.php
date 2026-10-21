<?php

namespace App\Filament\Resources\ProgresSpbeResource\Pages;

use App\Filament\Resources\ProgresSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgresSpbes extends ListRecords
{
    protected static string $resource = ProgresSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
