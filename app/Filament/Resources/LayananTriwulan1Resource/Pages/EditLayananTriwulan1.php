<?php

namespace App\Filament\Resources\LayananTriwulan1Resource\Pages;

use App\Filament\Resources\LayananTriwulan1Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananTriwulan1 extends EditRecord
{
    protected static string $resource = LayananTriwulan1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
