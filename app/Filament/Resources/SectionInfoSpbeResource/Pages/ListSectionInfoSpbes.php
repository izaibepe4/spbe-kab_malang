<?php

namespace App\Filament\Resources\SectionInfoSpbeResource\Pages;

use App\Filament\Resources\SectionInfoSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionInfoSpbes extends ListRecords
{
    protected static string $resource = SectionInfoSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
