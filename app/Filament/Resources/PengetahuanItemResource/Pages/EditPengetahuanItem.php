<?php

namespace App\Filament\Resources\PengetahuanItemResource\Pages;

use App\Filament\Resources\PengetahuanItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengetahuanItem extends EditRecord
{
    protected static string $resource = PengetahuanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
