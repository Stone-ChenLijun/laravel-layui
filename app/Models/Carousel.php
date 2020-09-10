<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carousel extends Model
{
    const TYPE_CAROUSEL = 1;
    const TYPE_NAV = 2;
    use SoftDeletes;

    protected $guarded = [];

    public function image()
    {
        return $this->belongsTo(AlbumImage::class, 'image_id', 'id');
    }
}
