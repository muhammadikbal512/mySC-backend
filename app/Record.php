<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Record extends Model
{
    protected $fillable = [
        'user_id', 'link', 'value', 'link', 'status'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function feedback() {
        return $this->morphMany('App\Feedback', 'feedbackable');
    }

    // public function feedback() {

    // }
}
