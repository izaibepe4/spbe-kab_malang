<?php

namespace App\Filament\Resources\SectionInfoSpbeResource\Pages;

use App\Filament\Resources\SectionInfoSpbeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionInfoSpbe extends EditRecord
{
    protected static string $resource = SectionInfoSpbeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
