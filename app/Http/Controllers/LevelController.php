<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Level;
use App\Experience;
use App\User;

use JWTAuth;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = Level::orderBy('id','desc')->get();
        $user   = JWTAuth::user();
        $exp    = Experience::where('user_id',$user->id)->first();

        return response()->json([
            'levels'        => $levels,
            'detail_user'   => $exp,
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            $request->validate([
                'name'          => 'required|max:30|min:3',
                'min_value'         => 'required|numeric|digits_between:1,4',
                'max_value'         => 'required|numeric|digits_between:1,4',
            ]);

            $level = Level::create($request->all());
            return response()->json([
                'Data'      => $level,
                'message'   => 'Level created successfully'
            ],201);
        } 
        catch(\exception $e){
            return response()->json(['message'=>'Bad Request'],400);
        }   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $level = Level::FindOrFail($id);
        return response()->json([
            'Level'         => $level,
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $level = Level::FindOrFail($id);
        $level->update($request->all());
        return response()->json([
            'Data'      => $level,
            'message'   => 'Data updated Successfully'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Level::destroy($id);
        return response()->json(['message'=>'Data deleted successfully'],204);
    }

    public function userAll(){
        $users = User::where('role_id',3)->where('is_active',1)->get();
        
        foreach($users as $user){
            $exp    = Experience::where('user_id',$user->id)->first();

            $data[] = [
                'detail_user'   => $user,
                'exp'           => $exp->total_value,
                'level'         => $exp->level()->first(),
            ];
        }

        $data = collect($data)->sortByDesc('exp')->all();

        $data = data_get($data,'*');

        return response()->json($data,200);

    }
}
