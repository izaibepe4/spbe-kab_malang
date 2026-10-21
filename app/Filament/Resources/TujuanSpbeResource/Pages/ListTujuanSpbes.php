<?php

namespace App\Filament\Resources\TujuanSpbeResource\Pages;

use App\Filament\Resources\TujuanSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTujuanSpbes extends ListRecords
{
    protected static string $resource = TujuanSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
