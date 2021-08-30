<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //Lock table for Model media with value 'medias'
    protected $table = 'medias';
    protected $fillable = [
        'path',
        'description',
    ];

    //relationship for User
    public function user(){
        return $this->hasOne('App\User');
    }

}
