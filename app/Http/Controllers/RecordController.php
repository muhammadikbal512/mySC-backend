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
    public function index() {

    }


    public function storeRecord(Request $request) {
        try {
            $user = JWTAuth::user();

            $record = Record::create([
                'user_id' => $record->$user_id,
                'value' => 1,
                'link' => $request->link
            ]);

            return response()->json([
                'Data' => [
                    'Record' => $record,
                    'user'  => $user,
                ],
                'Message' => 'Data created successfully'
            ], 201);
        }
        catch (Exception $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }
}
