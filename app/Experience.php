<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'user_id','total_sc','total_aic','level_id'
    ];

    //Relationship for Level
    public function level(){
        return $this->belongsTo('App\Level');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function media(){
        return $this->belongsTo('App\Media');
    }

    public function team() {
        return $this->belongsTo('App\Team');
    }
}
