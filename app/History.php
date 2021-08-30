<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'user_id','badge_id','type','date',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function badge(){
        return $this->belongsTo('App\Badge');
    }

    public function media(){
        return $this->hasOneThrough('App\Badge','App\Media');
    }
}
