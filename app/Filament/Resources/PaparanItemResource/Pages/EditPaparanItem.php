<?php

namespace App\Filament\Resources\PaparanItemResource\Pages;

use App\Filament\Resources\PaparanItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaparanItem extends EditRecord
{
    protected static string $resource = PaparanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
