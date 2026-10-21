<?php

namespace App\Filament\Resources\PengetahuanCategoryResource\Pages;

use App\Filament\Resources\PengetahuanCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengetahuanCategory extends EditRecord
{
    protected static string $resource = PengetahuanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
