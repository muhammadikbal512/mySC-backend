<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index()
    {
        $user = User::with('media','role', 'team')->select('id','name','email','media_id','role_id','is_active', 'team')->get();
        return response()->json($user,200);
    }

    public function getUserId($id) {
        $user   = User::whereId($id)->first();

        return response()->json($user,200);
    }

    //get all dosen
    public function getDosen() {
        $users = User::whereRoleId(2)->get();
        return response()->json([
            'dropdown_list' =>  $users
        ],200);
    }

    public function store(Request $request)
    {
        // try{
            if(User::whereEmail($request->email)->first())
            {
                return response()->json(['message'=>'User has already exist'],409);
            }

            $request->validate([
                'name'      => 'required|min:3|max:50|regex:/^[\pL\s\-]+$/u',
                'email'     => 'required',
                'team'      => 'required'
            ]);

            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'provider_id'   => 123,
                'password'      => Hash::make(123),
                'team'          => $request->team,
                'role_id'       => 1,
                'is_active'     => 1,
                'media_id'      => 100,
            ]);

            $user->experience()->create([
                'total_sc' => 0,
                'total_aic' => 0,
            ]);

            return response()->json([
                'Data'      => $user,
                'message'   => 'User created successfully'
            ],201);
        // }
        // catch (\Exception $e){
        //     return response()->json(['message'=>'Bad Request'],400);
        //     // dd($e);
        // }
    }
    public function show($id)
    {
        $user = User::FindOrFail($id);
        
        return response()->json([
            'User'  => [
                'Detail_user'   => $user,
                'Role'          => $user->role()->select('name')->get(),
                'Media'         => $user->media()->select('path')->get(),

            ],
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
        try{
            $user = User::FindOrFail($id)->update($request->all());
            return response()->json([
                'User'      => $user,
                'message'   => 'Data updated successfully'
            ],200);
        }
        catch (\exception $e){
            return response()->json([
                'message' =>'Bad Request'
            ],400);
        }
    }

    public function create()
    {
        $roles      = Role::select('id','name')->get();
        return response()->json([
            'dropdown_list' => $roles,
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
        User::destroy($id);
        return response()->json(['message'=>'Data deleted successfully'],204);
    }

    //Attach User to Difficulty to become a Reviewer 
    public function attach(Request $request,$id){
        $user = User::FindOrFail($id);
        $user->difficulties()->attach($request->difficulty_id);
        return response()->json([
            'User'          => $user,
            'Difficulty'    => $user->difficulties()->get(),
            'message'       => 'Reviewer added successfully'
        ],200);
    }

}
