<?php

namespace App\Filament\Resources\LayananTriwulan1Resource\Pages;

use App\Filament\Resources\LayananTriwulan1Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananTriwulan1s extends ListRecords
{
    protected static string $resource = LayananTriwulan1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
