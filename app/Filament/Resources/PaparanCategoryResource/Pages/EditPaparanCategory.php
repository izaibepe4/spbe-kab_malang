<?php

namespace App\Filament\Resources\PaparanCategoryResource\Pages;

use App\Filament\Resources\PaparanCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaparanCategory extends EditRecord
{
    protected static string $resource = PaparanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
