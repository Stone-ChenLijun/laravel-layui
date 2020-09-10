<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRecord extends Model
{
    const TYPE_REGISTER = 'register';

    const TYPE_ARRAY = [
        self::TYPE_REGISTER,
    ];
}
