<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengetahuanItem extends Model
{
    protected $fillable = [
        'category_id',
        'judul_item',
        'file_pdf_path',
    ];

    public function category()
    {
        return $this->belongsTo(PengetahuanCategory::class, 'category_id');
    }
}
