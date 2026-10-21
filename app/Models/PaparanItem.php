<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaparanItem extends Model
{
    protected $fillable = [
        'category_id',
        'judul_item',
        'tipe_embed',
        'embed_url',
        'file_pdf_path',
    ];

    public function category()
    {
        return $this->belongsTo(PaparanCategory::class, 'category_id');
    }
}
