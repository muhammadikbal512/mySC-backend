<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Aic;
use App\User;
use App\Experience;
use App\Record;
use App\Level;
use App\Media;

use JWTAuth;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use App\Mail\ClaimAIC;

class AIController extends Controller
{

    public function allRecord() {
        $user   =   JWTAuth::user();
        $records = Aic::with('user')->whereDosenId($user->id)->whereDate('created_at', today())->get();
        return response()->json([
                'Data' => [
                    'Record' => $records,
                ],
            ], 201);
    }

    public function allRecordRedeemed() {
        $user   =   JWTAuth::user();
        $records = Aic::with('user')->whereDosenId($user->id)->whereStatus('Approved', today())->get();
        return response()->json([
                'Data' => [
                    'Record' => $records,
                ],
            ], 201);
    }

    public function showAllAIC() {
        $user   =   JWTAuth::user();
        $records = Aic::with('user')->whereDosenId($user->id)->whereStatus('Approved')->get();
        return response()->json([
                'Data' => [
                    'Record' => $records,
                ],
            ], 201);
    }

    public function showRecord() {
        $user = JWTAuth::user();

        $claimaic = Aic::with('user')->whereUserId($user->id)->whereDate('created_at', today())->get();
        return response()->json([
            'Data' => [
                'Record' => $claimaic,
            ],
        ], 201);
    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();

            $record = Aic::create([
                'user_id'       => $user->id,
                'value'         => $request->value,
                'dosen_id'      => $request->dosen_id,
                'rekening'      => $request->rekening
            ]);
            $result = User::whereId($request->dosen_id)->first();
            $data = [
                'name'        => $user->name,
                'value'       => $request->value,
                'dosen'       => $result->name,
                'rekening'    => $request->rekening
            ];
            Mail::to($result->email)->send(new ClaimAIC($data));
            return response()->json([
                'Data' => [
                    'Record' => $record,
                    'User'   => $record->user()->get(),
                    // 'Dosen'  => $result,
                ],
                'Message' => 'Data created successfully'
            ], 201);
            
        }
        catch (Exception $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }

    public function claimAllAIC() {
        $aic = Aic::whereStatus('pending')->get();

        foreach($aic as $aic) {
            $user = JWTAuth::user();
            $exp = Experience::whereUserId($aic->user_id)->first();

            $result = $aic->feedback()->create([
                'Approve'   => 1,
                'user_id'   => $user->id,
            ]);

            $this->checkStatus($aic);
            $tes = $aic->whereStatus('Approved')->sum('value');
            $exp->total_aic = $exp->total_aic - $aic->value;
            $exp->claimaic = $tes;

            $exp->save();
        };

        return response()->json([
            'Record'    => $aic,
            'data'      => $result,
            'claimed'   => $tes,
            'aic'       => $exp->total_aic,
            'message'=>'Record has been approved'],201);
    }

    public function claimAIC(Request $request, $id) {

            $user = JWTAuth::user();
            $aic = Aic::FindOrFail($id);
            $exp = Experience::whereUserId($aic->user_id)->first();



            $request->validate([
                'Approve'      => 'numeric',
                ''
            ]);

            $result = $aic->feedback()->create([
                'Approve'       => $request->Approve,
                'user_id'       => $user->id,
            ]);

            $this->checkStatus($aic);
            $tes = $aic->whereStatus('Approved')->sum('value');
            $exp->total_aic = $exp->total_aic - $aic->value;
            $exp->claimaic = $tes;

            $exp->save();

            return response()->json([
                'Record'    => $aic,
                'data'      => $result,
                'claimed'   => $tes,
                'aic'       => $exp->total_aic,

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
