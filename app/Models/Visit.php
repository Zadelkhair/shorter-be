<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $guarded = [];

    // url belongs to a user
    public function url()
    {
        return $this->belongsTo(Url::class);
    }

}
