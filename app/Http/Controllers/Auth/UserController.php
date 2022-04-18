<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        $request->user()->id;
        $user = User::where("id", auth()->user()->id)->with("machines","machines.task","machines.task.user")->first();
        return response()->json( $user);
    }

    public function list(){
        $users = User::all();
        return response()->json( $users);
    }
    
    public function addUser(Request $request){
        
        $user = User::updateOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 
                'password' => bcrypt($request->password), 
                'role' => $request->role
            ]
        );
        return response()->json( $user);
    }
    public function deleteuser(Request $request){
        $user = User::where("id", $request->id)->delete();
        return response()->json( $user);
    }
}
