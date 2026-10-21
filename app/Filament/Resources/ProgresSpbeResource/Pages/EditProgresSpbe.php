<?php

namespace App\Filament\Resources\ProgresSpbeResource\Pages;

use App\Filament\Resources\ProgresSpbeResource;
use App\Filament\Traits\LogsActivity;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgresSpbe extends EditRecord
{
    use LogsActivity;

    protected static string $resource = ProgresSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterDelete(): void
    {
        $this->logDelete($this->record);
    }
}
