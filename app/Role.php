<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name'
    ];

    #relationship to User
    public function user() {
        return $this->hasOne('App\User');
    }
}
