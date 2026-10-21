<?php

namespace App\Filament\Resources\RegulasiCategoryResource\Pages;

use App\Filament\Resources\RegulasiCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegulasiCategories extends ListRecords
{
    protected static string $resource = RegulasiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
