<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class SessionController extends Controller {

    public function authenticate(Request $request) {

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'Email cannot be blank',
            'email.email' => 'Invalid Email',
            'password.required' => 'Password cannot be blank'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes()) {
            //$credentials = $request->only('email', 'password');

            $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

            $token = JWTAuth::attempt($credentials);

            if ($token) {

                $user = JWTAuth::setToken($token)->toUser();

                return response()->json([
                            'message' => 'Welcome ' . $user->first_name . ' ' . $user->last_name,
                            'success' => true,
                            'data' => array(
                                'token' => $token,
                                'user_id' => $user->id
                            ),
                            'credentials' => $credentials
                ]);
            } else {

                return response()->json([
                            'message' => 'Incorrect Email or Password',
                            'success' => false,
                            'token' => $token,
                            'credentials' => $credentials
                ]);
            }
        } else {

            return response()->json([
                        'message' => $validator->messages()->toArray(),
                        'success' => false
            ]);
        }
    }

}
