<?php

namespace App\Filament\Resources\SopItemResource\Pages;

use App\Filament\Resources\SopItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSopItems extends ListRecords
{
    protected static string $resource = SopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
