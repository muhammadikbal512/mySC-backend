<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;
use App\User;

use JWTAuth;

class TeamController extends Controller
{
    public function addTeam(Request $request) {
        try {
            $user = JWTAuth::user();

            $team = Team::create([
                'team'     =>   $request->team,
            ]);

            return response()->json([
                'Data' => $team,
                'Message' => 'Data created successfully'
            ], 201);
            
        }
        catch (Exception $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }

    public function index() {
        $teams = Team::select('id', 'team')->get();
        return response()->json([
            'dropdown_list' => $teams,
        ], 200);
    }

    public function joinTeam($id) {
        $user = JWTAuth::User();

        $teams = Team::select('id', 'team')->get();

        $join = $user->update([
            'team'  =>  $id,
        ]);

        return response()->json([
            'team'  =>  $join,
            'Message'   =>  'Success'
        ]);
    }
}
