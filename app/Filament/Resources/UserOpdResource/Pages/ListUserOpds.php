<?php

namespace App\Filament\Resources\UserOpdResource\Pages;

use App\Filament\Resources\UserOpdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserOpds extends ListRecords
{
    protected static string $resource = UserOpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
