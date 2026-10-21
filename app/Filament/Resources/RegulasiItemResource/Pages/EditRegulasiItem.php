<?php

namespace App\Filament\Resources\RegulasiItemResource\Pages;

use App\Filament\Resources\RegulasiItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegulasiItem extends EditRecord
{
    protected static string $resource = RegulasiItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
