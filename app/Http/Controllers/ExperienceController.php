<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Experience;
use App\Record;
use App\Level;
use App\User;
use App\Media;

use Carbon\Carbon;

use JWTAuth;

class ExperienceController extends Controller
{
    //Get Exp for User Active
    public function exp(){
        $user = JWTAuth::user();

        $exp = Experience::with('user')->whereUserId($user->id)->first();
        return response()->json($exp,200);
    }

    //Get Exp for Specific User
    public function expId($id){

        $exp    = Experience::with('user')->whereUserId($id)->first();
        $user   = User::where('id',$id)->first();
        return response()->json([
            'detail_exp'    => $exp,
            'media'         => $user->media->path,
        ],200);
    }

    //Update All Exp to All User
    public function mass(){
        $id = Experience::pluck('user_id');

        foreach($id as $id){

            $this->massExperience($id);
            $exp = Experience::whereUserId($id);

        }
        
        return response()->json($exp->get(),200);
        
    }

    //method for Check the Total Exp for every User
    function massExperience($id){
        $exp        = Experience::whereUserId($id)->first();
        $sum        = Record::whereUserId($id)->whereStatus('Approved')->sum('value');
        $level      = Level::where('min_value','<=',$sum)->where('max_value','>=',$sum)->first();

        if($exp->total_value != $sum){
            $exp->update([
                'total_sc'        => $sum,
                'level_id'        => $level->id,
            ]);
        }

        return true;
    }

    //Progress Level
    public function progress($id){
    
        $exp            = Experience::where('user_id',$id)->first();

        $levelMax       = $exp->level->max_value;

        $nextLevel      = Level::where('min_value',$levelMax+1)->first();
        
        return response()->json([
            'detail_user'   => $exp->user()->first(),
            'total_sc'      => $exp->total_value,
            'detail_level'  => $exp->level()->first(),
            'next_level'    => $nextLevel, 
        ],200);

    }

    public function claimSC() {

        $user = JWTAuth::user();

        $exp = Experience::whereUserId($user->id)->first();
        
            for($i=10; $exp->total_sc >= $i; $i+=10){
                $margin = $i - $exp->total_sc;
                if ($margin>0){
                    $exp->total_aic = ($i - 10)/10;
                } 
                else {
                    $exp->total_aic = $i / 10;
                } 
            }
            // $exp->total_aic = $exp->total_aic - $exp->claimaic;

            $exp->save();


    }

    public function claimAIC() {
        $user = JWTAuth::user();

        $exp = Experience::whereUserId($user->id)->first();

        $exps = $exp->total_aic - 1;
        // dd($exps);


        $exp->save();

        dd($exp);


        // return response()->json([
        //     'Message'   =>  'Claim AIC Success !'
        // ]);
    }

   

}