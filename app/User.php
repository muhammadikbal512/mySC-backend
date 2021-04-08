<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','provider_id','role_id','media_id','is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     //Relationship to Media
     public function media(){
        return $this->belongsTo('App\Media');
    }

    //Relationship to Role
    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function isDosen(){
        if($this->role->name == 'dosen' && $this->is_active == 1){
            return true;
        }
        return false;
    }

    //method tob check the Role for User -> Lecturer
    public function isMahasiswa(){
        if($this->role->name == 'mahasiswa' && $this->is_active == 1){
            return true;
        }
        return false;
    }

    //Relationship to Experience
    public function experience(){
        return $this->hasOne('App\Experience');
    }

}
