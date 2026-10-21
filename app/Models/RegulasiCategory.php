<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulasiCategory extends Model
{
    protected $fillable = [
        'nama_kategori',
    ];

    public function items()
    {
        return $this->hasMany(RegulasiItem::class, 'category_id');
    }
}
