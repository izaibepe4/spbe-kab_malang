<?php

namespace App\Filament\Resources\RegulasiCategoryResource\Pages;

use App\Filament\Resources\RegulasiCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegulasiCategory extends EditRecord
{
    protected static string $resource = RegulasiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
