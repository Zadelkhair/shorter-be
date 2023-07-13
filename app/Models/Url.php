<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $guarded = [];

    // add getter
    protected $appends = ['expires_in'];

    // url belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // url has many visits
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    // getter how many seconds remaining for the url to expire
    public function getExpiresInAttribute()
    {

        $duration = $this->duration;

        // get created at
        $createdAt = $this->created_at;

        // get current time
        $now = now();

        // get difference in seconds
        $diffInSeconds = $now->diffInSeconds($createdAt);

        // get remaining seconds
        $remainingSeconds = $duration - $diffInSeconds;

        return $remainingSeconds;

    }

}
