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
        $user = $request->user();
        if ($request->score > $user->score) {
            $user->score = $request->score;
            $user->save();
        }

        return response()->json([
            'score' => $request->score,
            'max_score' => $user->score
        ]);

    }

    public function scores()
    {
        $login = null;
        if (\request()->user()) {
            $login = \request()->user();
        }
        $i = 0;
        $users = User::all()->map(function ($user) use ($i, $login) {
            $current = false;
            if ($user->id === $login->id) {
                $current = true;
            }
            $i++;
            return [
                'id' => $user->id,
                'username' => $user->username,
                'score' => $user->score,
                'rank' => $i,
                'current' => $current,
            ];
        })->sortByDesc('score');

        $current = $users->where('current', true)->first();
        return response()->json([
            'users' => $users,
            'current_user' => $current
        ]);
    }
}
