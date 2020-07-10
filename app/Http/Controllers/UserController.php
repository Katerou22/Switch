<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{


    public function register(Request $request)
    {


        $user = User::where('username', $request->username)->first();

        if ($user !== null) {
            return response()->json([
                'message' => 'User Exists'
            ], 400);
        }


        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(8),
        ]);


        return response()->json([
            'token' => $user->api_token
        ]);

    }

    public function login(Request $request)
    {


        $user = User::where('username', $request->username)->first();

        if ($user === null) {
            return response()->json([
                'message' => 'User not found'
            ], 400);
        }


        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $user->api_token
            ]);

        } else {
            if ($user !== null) {
                return response()->json([
                    'message' => 'Password in incorrect'
                ], 400);
            }

        }
    }

    public function checkIfUsernameExists(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user !== null) {
            return response()->json([
                'status' => false
            ]);
        } else {
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function score(Request $request)
    {
        dd($request->user());
//        $user = auth()->guard('api')->user();
//        if ($user === null) {
//            return response()->json([
//                'message' => 'User Not Found'
//            ], 404);
//        }
//        $user->score = $request->score;
//        $user->save();
//        return response()->json([
//            'score' => $user->score
//        ]);

    }

    public function scores()
    {
        $users = User::all()->sortByDesc('score');
        return response()->json([
            'users' => $users
        ]);
    }
}
