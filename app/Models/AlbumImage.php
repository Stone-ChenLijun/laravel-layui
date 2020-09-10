<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AlbumImage extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function getPreviewUrlAttribute()
    {
        return self::getPreviewUrl($this->getAttributeValue('path'));
    }

    public static function getPreviewUrl($storagePath)
    {
        return Storage::url($storagePath);
    }
}
