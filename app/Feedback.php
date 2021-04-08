<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'like','dislike','user_id'
    ];

    //Relationship polymorphic
    public function feedbackable(){
        return $this->morphTo('App\Post');
    }

    //Relationship to User
    public function user(){
        return $this->belongsTo('App\User');
    }
}
