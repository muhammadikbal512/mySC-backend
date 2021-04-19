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
    //get All Records
    public function allRecord() {
        $records = Record::with('user')->get();
        return response()->json($records, 200);
    }

    //get all Record Pending
    public function recordPending() {
        $records = Record::with('user')->whereStatus('pending')->get();
        return response()->json($records, 200);
    }

    //get all Record Pending by id
    public function pendingId($id) {
        $records    = Record::with('user')->whereUserId($id)->get();
        $users  = $users->whereStatus('pending')->get();
        return response()->json($users,200);
    }

    public function recordVerified() {
        $records = Record::with('user')->whereStatus('Approved')->get();
        return response()->json($records, 200);
    }

    public function recordDenied() {
        $records = Record::with('user')->whereStatus('Rejected')->get();
        return response()->json($records, 200);
    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();
            // dd($user);

            $record = Record::create([
                'user_id'       => $user->id,
                'value'         => 1,
                'link'          => $request->link,
                'dosen_id'      => $request->id
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

    public function userPending($id) {
        

        // $date = $request->query('date');
        // if($date == ""){
        //     $date = now();
        // }
        $record    = Record::with('user')->whereStatus("pending")->whereUserId($id)->get();
        $user   = User::where('id',$id)->first();
        return response()->json([
            'detail_record'    => $record,
            'media'         => $user->media->path,
        ],200);


    }

    public function userApproved($id) {
        

        // $date = $request->query('date');
        // if($date == ""){
        //     $date = now();
        // }
        $record    = Record::with('user')->whereStatus("Approved")->whereUserId($id)->get();
        $user   = User::where('id',$id)->first();
        return response()->json([
            'detail_record'    => $record,
            'media'         => $user->media->path,
        ],200);


    }

    public function userRejected($id) {
        

        // $date = $request->query('date');
        // if($date == ""){
        //     $date = now();
        // }
        $record    = Record::with('user')->whereStatus("Rejected")->whereUserId($id)->get();
        $user   = User::where('id',$id)->first();
        return response()->json([
            'detail_record'    => $record,
        ],200);


    }

    public function feedbackApprove(Request $request,$id){

        // try{
            $user   = JWTAuth::user();
            $record = Record::FindOrFail($id);
            
            $request->validate([
                'Approve'      => 'numeric',
            ]);

            $result = $record->feedback()->create([
                'Approve'       => $request->Approve,
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

    public function feedbackReject(Request $request,$id){

        // try{
            $user   = JWTAuth::user();
            $record = Record::FindOrFail($id);
            
            $request->validate([
                'Reject'   => 'numeric',
            ]);

            $result = $record->feedback()->create([
                'Reject'        => $request->Reject,
                'user_id'       => $user->id,
            ]);

            $this->checkDisapprove($record);
            
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
        return  $record;
    }

    function checkDisapprove($record){
        $sum = $record->feedback()->sum('Reject');
        if($sum > 0){
            $record->update([
                'status'    => 'Rejected'
            ]);
        }
        return  $record;

    }

    function claimSC() {
        
    }
}
