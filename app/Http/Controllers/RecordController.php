<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Record;
use App\User;
use App\Experience;

use JWTAuth;
use Carbon\Carbon;

class RecordController extends Controller
{

    public function allRecord() {
        $records = Record::with('user')->get();
        return response()->json($records, 200);
    }

    public function recordPending() {
        $records = Record::with('user')->whereStatus('pending')->get();
        return response()->json($records, 200);
    }

    public function recordVerified() {
        $records = Record::with('user')->whereStatus('Approved')->get();
        return response()->json($records, 200);
    }

    public function recordDenied() {
        $records = Record::with('user')->whereStatus('Disapprove')->get();
        return response()->json($records, 200);
    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();
            // dd($user);

            $record = Record::create([
                'user_id' => $user->id,
                'value'   => 1,
                'link'    => $request->link
            ]);

            return response()->json([
                'Data' => [
                    'Record' => $record,
                    'User'   => $record->user()->get(),
                ],
                'Message' => 'Data created successfully'
            ], 201);
        }
        catch (Exception $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }

    public function userHistory(Request $request,$id) {
        $request->validate([
            'date'  => 'date'
        ]);

        $record     = Record::FindOrFail($id);
        $user       = JWTAuth::user();
        $date       = Carbon::parse($request->date)->setTimeZone('Asia/Jakarta')->setTime(0,0,0); 

            $records    = $record->records()->where('records.created_at','pending',$date->toDateString().'%')->where('records.user_id','=',$user->id)->get();

            foreach($records as $rec){
                
                // $sum = $rec->feedback()->sum('like');
                $data[]     = [
                    'detail_record' => $rec,
                    'status'        => $rec->status,
                    'user'          => $rec->user->name,
                ];
            }

        $exp = Record::where('user_id','=',$user->id)->where('status','=','Approved')->where('created_at','like',$date->toDateString().'%')->sum('value');

        return response()->json([
            'data'      =>  $data,
            'value'     =>  $exp,

        ],200);
    }

    public function feedback(Request $request,$id){

        // try{
            $user   = JWTAuth::user();
            $record = Record::FindOrFail($id);
            
            $request->validate([
                'Approve'      => 'numeric',
                'Disapprove'   => 'numeric',
            ]);

            $result = $record->feedback()->create([
                'Approve'       => $request->Approve,
                'Disapprove'    => $request->Disapprove,
                'user_id'       => $user->id,
            ]);

            $this->checkStatus($record);
            
            return response()->json([
                'Record' => $record,
                'data'   => $result,
                'message'=>'Record has been approved'],201);

        // }
        // catch(\exception $e){
        //     return response()->json(['message'=>'Bad request'],400);
        // }
    }

    //Method to check and Update status a record
    function checkStatus($record){
        $sum = $record->feedback()->sum('Approve');
        if($sum > 0){
            $record->update([
                'status'    => 'Approved'
            ]);
        }
        else {
            $record->update([
                'status'    =>  'Disapprove'
            ]);
        }

        return  $record;

    }
}
