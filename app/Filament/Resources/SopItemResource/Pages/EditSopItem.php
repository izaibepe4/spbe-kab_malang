<?php

namespace App\Filament\Resources\SopItemResource\Pages;

use App\Filament\Resources\SopItemResource;
use App\Filament\Traits\LogsActivity;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSopItem extends EditRecord
{
    use LogsActivity;

    protected static string $resource = SopItemResource::class;

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
