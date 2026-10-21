<?php

namespace App\Filament\Resources\TujuanSpbeResource\Pages;

use App\Filament\Resources\TujuanSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTujuanSpbe extends EditRecord
{
    protected static string $resource = TujuanSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
