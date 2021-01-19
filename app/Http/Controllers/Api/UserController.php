<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'national_id' => 'required|numeric',
            'password' => 'required',

        ]);

        $user = User::where('national_id', $request->national_id)->first();

        if (!$user || !($user->password == hash("sha256", $request->password))) {
            // throw ValidationException::withMessages([
            //     'email' => ['The provided credentials are incorrect.'],
            // ]);
            //if authentication is unsuccessfull, notice how I return json parameters
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID number or Password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $user->createToken(Str::random(6))->plainTextToken,
            'user' =>  $user
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|numeric',
            'phone' => 'required|unique:users|regex:/(0)[0-9]{10}/',
            'email' => 'required|email|unique:users',
            'password' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $input = $request->all();
        $input['password'] = hash("sha256", $input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken(Str::random(6))->plainTextToken;
        return response()->json([
            'success' => true,
            'token' => $success,
            'user' => $user
        ]);
    }
}
