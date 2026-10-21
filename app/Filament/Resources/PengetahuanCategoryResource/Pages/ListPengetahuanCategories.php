<?php

namespace App\Filament\Resources\PengetahuanCategoryResource\Pages;

use App\Filament\Resources\PengetahuanCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengetahuanCategories extends ListRecords
{
    protected static string $resource = PengetahuanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
