<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Aic;
use App\User;
use App\Experience;

use JWTAuth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

class AIController extends Controller
{

    public function allRecord() {
        $user   =   JWTAuth::user();
        $records = Aic::with('user')->whereDosenId($user->id)->get();
        return response()->json([
                'Data' => [
                    'Record' => $records,
                ],
            ], 201);
    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();
            // dd($user);

            $record = Aic::create([
                'user_id'       => $user->id,
                'value'         => $request->value,
                'dosen_id'      => $request->dosen_id
            ]);
            // dd($record);
            // $result = User::whereId($request->dosen_id)->first();
            // Mail::to($result->email)->send(new ScNotif);
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
}
