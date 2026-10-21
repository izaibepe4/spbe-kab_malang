<?php

namespace App\Filament\Resources\LayananTriwulan4Resource\Pages;

use App\Filament\Resources\LayananTriwulan4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananTriwulan4 extends EditRecord
{
    protected static string $resource = LayananTriwulan4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
