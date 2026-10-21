<?php

namespace App\Filament\Resources\LayananTriwulan4Resource\Pages;

use App\Filament\Resources\LayananTriwulan4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananTriwulan4s extends ListRecords
{
    protected static string $resource = LayananTriwulan4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
