<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SopItem extends Model
{
    protected $fillable = [
        'slug',
        'file_path',
        'tipe',
        'ringkasan',
    ];

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
