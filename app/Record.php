<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Record extends Model
{
    protected $fillable =[
        'user_id','link','status','value'
    ];

    //Relationship to User
    public function user(){
        return $this->belongsTo('App\User');
    }

    //Relation to Feedback (Polymorphic)
    public function feedback(){
        return $this->morphMany('App\Feedback','feedbackable');
    }

    //Create Accessor for created_at
    public function getCreatedAtAttribute(){
        $date = Carbon::parse($this->attributes['created_at'])->setTimeZone('Asia/Jakarta')->format('D, d F Y H:i');
        return $date;
    }


}
