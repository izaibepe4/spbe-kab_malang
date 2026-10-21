<?php

namespace App\Filament\Resources\LayananTriwulan2Resource\Pages;

use App\Filament\Resources\LayananTriwulan2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananTriwulan2s extends ListRecords
{
    protected static string $resource = LayananTriwulan2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
