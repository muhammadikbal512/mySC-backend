<?php

namespace App\Http\Controllers;

use App\Record;
use App\User;
use App\Experience;
use App\Givesc;

use JWTAuth;
use Illuminate\Http\Request;

class GivescController extends Controller
{
    public function getRecords() {
        $users   = User::where('role_id',1)->get();

        foreach($users as $user){
            $exp            = Experience::where('user_id',$user->id)->first();


            $data[]     = [
                'detail_user'   => $user,
                'level'         => $exp->level()->first(),
                'total_sc'      => $exp->total_sc,
                'total_aic'     => $exp->total_aic,
                'claimaic'      => $exp->claimaic
            ];
        }

        $data = collect($data)->sortByDesc('total_sc')->skip(0)->all();
        
        $array = data_get($data,'*');
        return response()->json([
            'details'   =>  $array,
            
         ],200);

    }
    public function GiveSC(Request $request) {
        try {
            $user = JWTAuth::user();

            $record = Record::create([
                'user_id'       => $request->user_id,
                'value'         => 1,
                'sc'            => $request->sc,
                'status'        => 'givesc',
                'dosen_id'      => $user->id
            ]);
            $result = User::whereId($user->id)->first();
            // $data = [
            //     'name'  => $user->name,
            //     'dosen' => $result->name,
            //     'sc'    => $request->link
            // ];
            // Mail::to($result->email)->send(new ScNotif($data));
            return response()->json([
                'Data' => [
                    'Record' => $record,
                    'User'   => $record->user()->get(),
                    'Dosen'  => $result,
                ],
                'Message' => 'Data created successfully'
            ], 201);
            
        }
        catch (Exception $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }

    public function allRecord() {
        $user   =   JWTAuth::user();
        $records = Record::with('user')->whereUserId($user->id)->whereStatus('givesc')->whereDate('created_at', today())->get();

        return response()->json([
                'Data' => [
                    'Record' => $records,
                    // 'Dosen' =>$dosen
                ],
            ], 201);
    }

    public function feedbackApproveGave(Request $request,$id){

        $user   = JWTAuth::user();
        $record = Record::FindOrFail($id);
        
        $request->validate([
            'Approve'      => 'numeric',
        ]);

        $result = $record->feedback()->create([
            'Approve'       => $request->Approve,
            'user_id'       => $user->id,
        ]);
        
        $results = $record->user()->first();
        // $data = [
        //     'murid'  => $results->name,
        // ];
        // Mail::to($results->email)->send(new ScNotifApproved($data));

        $this->checkStatus($record);

        return response()->json([
            'Record' => $record,
            'data'   => $result,
            'message'=>'Record has been approved'],201);
    }


    //Method to check and Update status a record
    function checkStatus($record){
        $sum = $record->feedback()->sum('Approve');
        if($sum > 0){
            $record->update([
                'status'    => 'Approved'
            ]);
        }
        return  $record;
    }
}
