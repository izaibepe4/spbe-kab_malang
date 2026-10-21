<?php

namespace App\Filament\Resources\LayananTriwulan3Resource\Pages;

use App\Filament\Resources\LayananTriwulan3Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananTriwulan3s extends ListRecords
{
    protected static string $resource = LayananTriwulan3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
