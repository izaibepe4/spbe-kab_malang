<?php

namespace App\Filament\Resources\IndikatorSpbeResource\Pages;

use App\Filament\Resources\IndikatorSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndikatorSpbe extends EditRecord
{
    protected static string $resource = IndikatorSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
