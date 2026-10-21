<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengetahuanCategory extends Model
{
    protected $fillable = [
        'nama_kategori',
    ];

    public function items()
    {
        return $this->hasMany(PengetahuanItem::class, 'category_id');
    }
}
