<?php

namespace App\Filament\Resources\AgendaImageResource\Pages;

use App\Filament\Resources\AgendaImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgendaImage extends EditRecord
{
    protected static string $resource = AgendaImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
