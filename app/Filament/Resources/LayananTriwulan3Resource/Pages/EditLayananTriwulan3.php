<?php

namespace App\Filament\Resources\LayananTriwulan3Resource\Pages;

use App\Filament\Resources\LayananTriwulan3Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananTriwulan3 extends EditRecord
{
    protected static string $resource = LayananTriwulan3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
