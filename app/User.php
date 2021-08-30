<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    //Generate Token JWT (Package from Tymon/JWTAuth)
    public function getJWTIdentifier(){
        return $this->getkey();
    }

    //Generate Token JWT (Package from Tymon/JWTAuth)
    public function getJWTCustomClaims(){
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','provider_id','role_id','media_id','is_active', 'dosen_id', 'team'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $with = ['role'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected $with = ['media'];

    //Relationship to Media
    public function media(){
        return $this->belongsTo('App\Media');
    }

    //Relationship to Role
    public function role(){
        return $this->belongsTo('App\Role');
    }

     //Relationship to Role
     public function team(){
        return $this->belongsTo('App\Team', 'team');
    }

    //method tob check the Role for User -> Administrator
    public function isAdmin(){
        if($this->role->name == 'admin' && $this->is_active == 1){
            return true;
        }
        return false;
    }

    //method tob check the Role for User -> Lecturer
    public function isDosen(){
        if($this->role->name == 'dosen' && $this->is_active == 1){
            return true;
        }
        return false;
    }

    //method tob check the Role for User - Student
    public function isMahasiswa(){
        if($this->role->name == 'mahasiswa' && $this->is_active == 1){
            return true;
        }
        return false;
    }

    //Relationship to Difficulty
    public function difficulties(){
        return $this->belongsToMany('App\Difficulty')->as('reviewer')->withTimestamps();
    }

    //Relationship to Experience
    public function experience(){
        return $this->hasOne('App\Experience');
    }

    //Relationship to Experience
    public function record(){
        return $this->hasOne('App\Record');
    }

    //Relationship to LoginHistory
    public function login(){
        return $this->hasMany('App\LoginHistory');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function history(){
        return $this->hasMany('App\History');
    }

    public function feedback(){
        return $this->hasMany('App\Feedback');
    }

}
