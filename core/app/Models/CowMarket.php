<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CowMarket extends Model
{
    protected $casts = [
        'html_classes' => 'object'
    ];
}
