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
        // $count      = Record::whereUserId($id)->whereStatus('Approved')->count();
        $level      = Level::where('min_value','<=',$sum)->where('max_value','>=',$sum)->first();

        if($exp->total_value != $sum){
            $exp->update([
                'total_sc'        => $sum,
                // 'total_quest'     => $count,
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

    // function BadgePath($badges,$user,$type,$date){
    //     //Get Badge
    //     if(count($badges)>0){
    //     foreach($badges as $badge){
    //         $mediaId    = $badge->badge->media_id;
    //         $media      = Media::where('id',$mediaId)->first();
    //         $path       = url('/').$media->path;
    //     }
    //     }
    //     else{
    //         $path       = 'none';
    //     }
    //     return $path;
    // }

}