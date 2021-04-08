<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title','body','user_id'
    ];

    //Relationship to User
    public function user(){
        return $this->belongsTo('App\User');
    }

    // //Relationship to Category
    // public function categories(){
    //     return $this->belongsToMany('App\Models\Category');
    // }

    //Relationship to Feedback (Polymorphic)

    public function feedback(){
        return $this->morphMany('App\Feedback','feedbackable');
    }
}
