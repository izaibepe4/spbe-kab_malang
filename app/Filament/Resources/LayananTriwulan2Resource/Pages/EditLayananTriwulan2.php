<?php

namespace App\Filament\Resources\LayananTriwulan2Resource\Pages;

use App\Filament\Resources\LayananTriwulan2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananTriwulan2 extends EditRecord
{
    protected static string $resource = LayananTriwulan2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
