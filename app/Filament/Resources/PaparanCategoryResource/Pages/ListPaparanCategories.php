<?php

namespace App\Filament\Resources\PaparanCategoryResource\Pages;

use App\Filament\Resources\PaparanCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaparanCategories extends ListRecords
{
    protected static string $resource = PaparanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
