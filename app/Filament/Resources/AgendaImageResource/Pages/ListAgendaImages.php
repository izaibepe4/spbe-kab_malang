<?php

namespace App\Filament\Resources\AgendaImageResource\Pages;

use App\Filament\Resources\AgendaImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgendaImages extends ListRecords
{
    protected static string $resource = AgendaImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
