<?php

namespace App\Filament\Resources\IndikatorSpbeResource\Pages;

use App\Filament\Resources\IndikatorSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndikatorSpbes extends ListRecords
{
    protected static string $resource = IndikatorSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
