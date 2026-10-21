<?php

namespace App\Filament\Resources\PengetahuanItemResource\Pages;

use App\Filament\Resources\PengetahuanItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengetahuanItem extends CreateRecord
{
    protected static string $resource = PengetahuanItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
