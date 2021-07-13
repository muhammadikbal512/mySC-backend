<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Store_aic extends Model
{
    protected $fillable =[
        'user_id','value','dosen_id'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function feedback(){
        return $this->morphMany('App\Feedback','feedbackable');
    }

    //Create Accessor for created_at
    public function getCreatedAtAttribute(){
        $date = Carbon::parse($this->attributes['created_at'])->setTimeZone('Asia/Jakarta')->format('D, d F Y H:i');
        return $date;
    }
}
