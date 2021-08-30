<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
    ];

    public function history(){
        return $this->hasOne('App\History');
    }

    public function media(){
        return $this->belongsTo('App\Media');
    }
}
