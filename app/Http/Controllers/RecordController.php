<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Record;
use App\User;
use App\Experience;
use App\Aic;

use JWTAuth;
use Carbon\Carbon;

use App\Mail\ScNotif;
use App\Mail\ScNotifApproved;
use Illuminate\Support\Facades\Mail;

class RecordController extends Controller
{
    //Secret Chamber 
    //get All Records
    public function allRecord() {
        $user   =   JWTAuth::user();
        $records = Record::with('user')->whereDosenId($user->id)->get();
        $verified = Record::with('user')->whereDosenId($user->id)->whereStatus('Approved')->get();
        $pending = Record::with('user')->whereDosenId($user->id)->whereStatus('pending')->get();
        $rejected = Record::with('user')->whereDosenId($user->id)->whereStatus('Rejected')->get();
        $AIC = Aic::with('user')->whereDosenId($user->id)->get();
        return response()->json([
                'Data' => [
                    'Record' => $records,
                    'Verified'   => $verified,
                    'Pending'  => $pending,
                    'Rejected'  =>  $rejected,
                    'Aic'       =>  $AIC
                ],
            ], 201);
    }
     //get all Record GiveSC
     public function giveScPending() {
       $user   =  JWTAuth::user();
        $records = Record::with('user')->whereDosenId($user->id)->whereStatus('givesc')->get();
        return response()->json($records, 200);
    }
    //get all Record Pending
    public function recordPending() {

        $user   =  JWTAuth::user();
        $records = Record::with('user')->whereDosenId($user->id)->whereStatus('pending')->get();
        return response()->json($records, 200);
    }

    //get all Record Pending by id
    public function pendingId($id) {
        $records    = Record::with('user')->whereUserId($id)->get();
        $users  = $users->whereStatus('pending')->get();
        return response()->json($users,200);
    }
    
    //get all Record Verified
    public function recordVerified(Request $request) {
        $user   =  JWTAuth::user();
        $date = $request->query('date');
        if ($date == null) {
            $date = now();
        }
        $records = Record::with('user')->whereDosenId($user->id)->whereStatus('Approved')->orderBy('id', 'DESC')->whereDate('created_at', $date)->get();

        return response()->json($records, 200);
    }

    //get all Record Rejected
    public function recordDenied() {
        $user   =  JWTAuth::user();
        $records = Record::with('user')->whereDosenId($user->id)->whereStatus('Rejected')->whereDate('created_at', today())->get();
        return response()->json($records, 200);
    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();
            // dd($user);

            $record = Record::create([
                'user_id'       => $user->id,
                'value'         => 1,
                'sc'            => $request->sc,
                'dosen_id'      => $request->dosen_id
            ]);
            $result = User::whereId($request->dosen_id)->first();
            $data = [
                'name'  => $user->name,
                'dosen' => $result->name,
                'sc'    => $request->sc
            ];
            Mail::to($result->email)->send(new ScNotif($data));
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

    public function userPending(Request $request) {
        

        $date = $request->query('date');
        if($date == ""){
            $date = now();
        }
        $user = JWTAuth::user();
        $record = Record::with('user')->whereUserId($user->id)->whereStatus("pending")->whereDate('created_at', $date)->get();
        return response()->json([
            'detail_record' =>  $record,
        ]);

    }

    public function userApproved() {
        
        $user = JWTAuth::user();
        $date = $request->query('date');
        if ($date == null) {
            $date = now();
        }
        $record = Record::with('user')->whereUserId($user->id)->whereStatus("Approved")->whereDate('created_at', $date)->get();
        return response()->json([
            'detail_record' =>  $record
        ]);


    }

    public function userRejected() {
        

        // $date = $request->query('date');
        // if($date == ""){
        //     $date = now();
        // }
        // $record    = Record::with('user')->whereStatus("Rejected")->whereUserId($id)->get();
        // $user   = User::where('id',$id)->first();
        // return response()->json([
        //     'detail_record'    => $record,
        // ],200);
        $user   = JWTAuth::User();
        $record = Record::with('user')->whereUserId($user->id)->whereStatus("Rejected")->whereDate('created_at', today())->get();
        return response()->json([
            'detail_record' =>  $record
        ]);


    }

    public function feedbackAllApprove(Request $request){

            $record = Record::whereStatus('pending')->get();

            foreach($record as $record) {
                $user   = JWTAuth::user();

                $request->validate([
                    'Approve'      => 'numeric',
                ]);
                $result = $record->feedback()->create([
                    'Approve'       => $request->Approve,
                    'user_id'       => $user->id,
                ]);
    
                $this->checkStatusAll($record);
                
            }
            return response()->json([
                'Record' => $record,
                'data'   => $result,
                'message'=>'Record has been approved'],201);
    }

     //Method to check and Update status a record
     function checkStatusAll($record){
        $sum = $record->feedback()->sum('Approve');
        if($sum > 0){
            $record->update([
                'status'    => 'Approved'
            ]);
        }
        return  $record;
    }

    public function feedbackApprove(Request $request,$id){

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
            $data = [
                'murid'  => $results->name,
            ];
            Mail::to($results->email)->send(new ScNotifApproved($data));

            $this->checkStatus($record);

            return response()->json([
                'Record' => $record,
                'data'   => $result,
                'message'=>'Record has been approved'],201);
    }

    public function feedbackReject(Request $request,$id){

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
            $this->checkStatus($record);
            $results = $record->user()->first();
            $data = [
                'name'  => $results->name,
            ];
            Mail::to($results->email)->send(new ScNotifApproved($results->name));
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

    function checkDisapprove($record){
        $sum = $record->feedback()->sum('Reject');
        if($sum > 0){
            $record->update([
                'status'    => 'Rejected'
            ]);
        }
        return  $record;

    }

    public function storeAIC(Request $request) {
        try {
            $user = JWTAuth::user();
            // dd($user);

            $record = Record::create([
                'user_id'       => $user->id,
                'aic'           => $request->aic,
                'dosen_id'      => $request->dosen_id
            ]);
            // dd($request->dosen_id);
            $result = User::whereId($request->dosen_id)->first();
            Mail::to($result->email)->send(new ScNotif);
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

     //Get Newest Record for User Active
     public function recordSc(){
        $user = JWTAuth::user();

        $record = Record::with('user')->whereUserId($user->id)->latest()->take(1)->get();
        return response()->json($record,200);
    }

    public function recordScId($id) {
        $record = Record::whereUserId($id)->latest()->take(1)->get();
        return response()->json($record,200);
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

    
}