<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulasiItem extends Model
{
    protected $fillable = [
        'category_id',
        'judul_regulasi',
        'file_pdf_path',
    ];

    public function category()
    {
        return $this->belongsTo(RegulasiCategory::class, 'category_id');
    }
}
