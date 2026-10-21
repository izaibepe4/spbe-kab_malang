<?php

namespace App\Filament\Resources\PengetahuanCategoryResource\Pages;

use App\Filament\Resources\PengetahuanCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengetahuanCategory extends CreateRecord
{
    protected static string $resource = PengetahuanCategoryResource::class;

    /**
     * Setelah berhasil create, redirect balik
     * ke halaman list (index), bukan tetap di form create.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
