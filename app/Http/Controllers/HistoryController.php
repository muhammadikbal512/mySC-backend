<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\History;
use App\Experience;
use App\Record;
use App\Level;
use App\User;
use App\Quest;
use App\Badge;
use Carbon\Carbon;

use JWTAuth;

class HistoryController extends Controller
{
    //Top 3 Rank Daily
    public function daily(){
        $date = Carbon::now()->setTimeZone('Asia/Jakarta')->subDays(1);

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){
        
            $experience = Experience::whereUserId($user->id)->first();
            
            $records    = Record::where('created_at','like',$date->toDateString().'%')->whereUserId($user->id)->whereStatus('verified')->sum('value');

            $count    = Record::where('created_at','like',$date->toDateString().'%')->whereUserId($user->id)->whereStatus('verified')->count();

            $data[]     = [
                'detail_user'   => $user,
                'media'         => $user->media->path,
                'level'         => $experience->level()->first(),
                'total_value'   => $records,
                'total_quest'   => $count,
                
            ];
        
        }

        $data   = collect($data)->sortByDesc('total_value')->skip(0)->take(3)->all();

        $array  = data_get($data,'*');
        
        $first      = data_get($array[0],'detail_user.id');
        $second     = data_get($array[1],'detail_user.id');
        $third      = data_get($array[2],'detail_user.id');

        //Store Badge for Daily Rank - 1st
        History::create([
            'user_id'   => $first,
            'badge_id'  => 1,
            'type'      => 'daily',
            'date'      => $date,
        ]);

        //Store Badge for Daily Rank - 2nd
        History::create([
            'user_id'   => $second,
            'badge_id'  => 2,
            'type'      => 'daily',
            'date'      => $date,
        ]);

        //Store Badge for Daily Rank - 3rd
        History::create([
            'user_id'   => $third,
            'badge_id'  => 3,
            'type'      => 'daily',
            'date'      => $date,
        ]);

        return response()->json([
            'data'      =>$array,
            'message'   => 'success'],200);
    }

    //Top 3 Rank Weekly
    public function weekly(){
        $dateTo     = Carbon::now()->setTimeZone('Asia/Jakarta');
        $dateFrom   = Carbon::now()->setTimeZone('Asia/Jakarta')->subDays(7);

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){

            $experience = Experience::whereUserId($user->id)->first();    

            $records    = Record::whereBetween('created_at',[$dateFrom,$dateTo])->whereUserId($user->id)->whereStatus('verified')->sum('value');


            $count    = Record::whereBetween('created_at',[$dateFrom,$dateTo])->whereUserId($user->id)->whereStatus('verified')->count();
            $data[]     = [
                'detail_user'   => $user,
                'media'         => $user->media->path,
                'level'         => $experience->level()->first(),
                'total_value'   => $records,
                'total_quest'    => $count,
            ];
        
        }

        $data   = collect($data)->sortByDesc('total_value')->skip(0)->take(3)->all();

        $array  = data_get($data,'*');
        
        $first      = data_get($array[0],'detail_user.id');
        $second     = data_get($array[1],'detail_user.id');
        $third      = data_get($array[2],'detail_user.id');

        //Store Badge for Weekly Rank - 1st
        History::create([
            'user_id'   => $first,
            'badge_id'  => 4,
            'type'      => 'weekly',
            'date'      => $dateFrom,
        ]);

        //Store Badge for Weekly Rank - 2nd
        History::create([
            'user_id'   => $second,
            'badge_id'  => 5,
            'type'      => 'weekly',
            'date'      => $dateFrom,
        ]);

        //Store Badge for Weekly Rank - 3rd
        History::create([
            'user_id'   => $third,
            'badge_id'  => 6,
            'type'      => 'weekly',
            'date'      => $dateFrom,
        ]);

        return response()->json([
            'data'      =>$array,
            'message'   => 'success'],200);

    }

    //Top 3 Rank Monthly
    public function monthly(){
        $date = Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1)->format('Y-m');

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){

            $experience = Experience::whereUserId($user->id)->first();        

            $records   = Record::where('created_at','like','%'.$date.'%')->whereStatus('verified')->whereUserId($user->id)->sum('value');

            $count   = Record::where('created_at','like','%'.$date.'%')->whereStatus('verified')->whereUserId($user->id)->count();
            $data[]     = [
                'detail_user'   => $user,
                'media'         => $user->media->path,
                'level'         => $experience->level()->first(),
                'total_value'   => $records,
                'total_quest'   => $count,
            ];       
        
        }

        $data   = collect($data)->sortByDesc('total_value')->skip(0)->take(3)->all();

        $array  = data_get($data,'*');
        
        $first      = data_get($array[0],'detail_user.id');
        $second     = data_get($array[1],'detail_user.id');
        $third      = data_get($array[2],'detail_user.id');

        //Store Badge for Monthly Rank - 1st
        History::create([
            'user_id'   => $first,
            'badge_id'  => 7,
            'type'      => 'monthly',
            'date'      => Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1),

        ]);

        //Store Badge for Monthly Rank - 2nd
        History::create([
            'user_id'   => $second,
            'badge_id'  => 8,
            'type'      => 'monthly',
            'date'      => Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1),
        ]);

        //Store Badge for Monthly Rank - 3rd
        History::create([
            'user_id'   => $third,
            'badge_id'  => 9,
            'type'      => 'monthly',
            'date'      => Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1),
        ]);

        return response()->json([
            'data'      =>$array,
            'message'   => 'success'],200);
    }

    //Method to store Badge Daily Hardworker
    public function dailyHardWorker(){
        $date = Carbon::now()->setTimeZone('Asia/Jakarta')->subDays(1);

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){         

            $quests   = Quest::where('difficulty_id',1)->count();
            $count    = Record::where('created_at','like',$date->toDateString().'%')->whereUserId($user->id)->whereStatus('verified')->count();

            if($count == $quests){
                $user->history()->create([
                    'badge_id'  => 10,
                    'type'      => 'daily',
                    'date'      => $date,
                ]);
            }


        }

        return response()->json(['message'=>'Success'],201);

    }

    //Method to store Badge Weekly Hardworker
    public function weeklyHardWorker(){
        $dateTo     = Carbon::now();
        $dateFrom   = Carbon::now()->subDays(7);

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){

            $quests   = Quest::where('difficulty_id',1)->count() * 5;
            $count    = Record::whereBetween('created_at',[$dateFrom,$dateTo])->whereUserId($user->id)->whereStatus('verified')->count();

            if($count == $quests){
                $user->history()->create([
                    'badge_id'  => 11,
                    'type'      => 'weekly',
                    'date'      => $dateFrom,
                ]);
            }

        
        }
        return response()->json(['message'=>'Success'],201);
        
    }

    //Method to store Badge Monthly Hardworker
    public function monthlyHardWorker(){
        $date       = Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1)->format('Y-m');
        
        //method to get a weekdays in month
        $workdays   = $this->workDays();

        $users = User::whereIsActive(1)->whereRoleId(3)->get();

        foreach($users as $user){         

            $quests   = Quest::where('difficulty_id',1)->count() * $workdays;
            $count   = Record::where('created_at','like','%'.$date.'%')->whereStatus('verified')->whereUserId($user->id)->count();

            if($count == $quests){
                $user->history()->create([
                    'badge_id'  => 12,
                    'type'      => 'monthly',
                    'date'      => Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1),
                ]);
            }


        }

        return response()->json(['message'=>'Success'],201);
    }

    //Method to get workdays in a month
    function workDays(){
        $days      = Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1)->daysInMonth;
        $date      = Carbon::now()->setTimeZone('Asia/Jakarta')->subMonths(1)->firstOfMonth();
        $workdays  = 0;
        
        while($days > 0){
            $day = $date->format('D');
            if($day != 'Sun' && $day != 'Sat'){
                $workdays++;
            }
            $days--;
            $date->addDay(1);
        }

        return $workdays;
         
    }

    //Show All Achievement in My Overview
    public function overview(){

        $badges = Badge::all();

        foreach($badges as $badge){
            $user   = JWTAuth::user();
            $count  = $badge->history()->where('user_id',$user->id)->count();

            if(Str::contains($badge->media->path,'https')){
                $path = $badge->media->path;
            }
            else{
                $path = url('/').$badge->media->path;
            }

            $data[] = [
                'badge'  => $badge,
                'media'  => $path,
                'total'  => $count,
            ];
        }

        $data = collect($data)->sortBy('badge.id');

        $data = data_get($data,'*');

        return response()->json($data,200);

        return response()->json($data,200);
        
    }

    //Show All Achievement in My Overview (Specific User)
    public function overviewId($id){

        $badges = Badge::all();

        foreach($badges as $badge){
            $user   = User::FindOrFail($id);
            $count  = $badge->history()->where('user_id',$user->id)->count();

            if(Str::contains($badge->media->path,'https')){
                $path = $badge->media->path;
            }
            else{
                $path = url('/').$badge->media->path;
            }

            $data[] = [
                'badge'  => $badge,
                'media'  => $path,
                'total'  => $count,
            ];
        }

        $data = collect($data)->sortBy('badge.id');

        $data = data_get($data,'*');

        return response()->json($data,200);

        return response()->json($data,200);
        
    }
}
