<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function children()
    {
        return $this->sub()->with('children');
    }

    public function sub()
    {
        return $this->hasMany(Album::class, 'parent_id', 'id');
    }
}
