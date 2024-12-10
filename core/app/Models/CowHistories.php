<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class CowHistories extends Model
{
    use GlobalStatus;

    protected $fillable = ['price'];
}
