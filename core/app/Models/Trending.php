<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Trending extends Model
{
    use GlobalStatus;

    public function scopeTrending($query)
    {
        $query->where('status', Status::ENABLE);
    }
    
    public function scopeRankOrdering($query)
    {
        return $query->orderByRaw('ranking = 0 ASC, ranking ASC');
    }

    public function marketData()
    {
        return $this->hasOne(MarketData::class, 'currency_id');
    }
}

