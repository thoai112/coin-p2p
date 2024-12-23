<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Trending extends Model
{
    use GlobalStatus;

    
    public function scopeRankOrdering($query)
    {
        return $query->orderByRaw('ranking = 0 ASC, ranking ASC');
    }
}

