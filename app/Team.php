<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'team'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
    
    public function experience() {
        return $this->hasOne('App\Experience');
    }
}
