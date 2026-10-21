<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaparanCategory extends Model
{
    protected $fillable = ['nama_kategori'];

    public function items()
    {
        return $this->hasMany(PaparanItem::class, 'category_id');
    }
}
