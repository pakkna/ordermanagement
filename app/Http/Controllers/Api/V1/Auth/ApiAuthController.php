<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;

class ApiAuthController extends Controller
{

    public function  __construct()
    {
        $this->middleware('auth:api', ['except' => ['registration', 'user_login']]);
    }

    protected function guard()
    {
        return Auth::guard('api');
    }

    public function registration(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'mobile' => 'required|regex:/(01)[0-9]{9}/|unique:users',
            'address' => 'required|string',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->ResponseJson(true, 'Validation Error.', $validator->messages()->all(), 422);
        } else {

            try {

                $user = User::create([
                    'firstname' => explode(" ", $request->name)[0] ?? '-',
                    'lastname' => explode(" ", $request->name)[1] ?? '-',
                    'name' => $request->name,
                    'username' => $request->mobile,
                    'mobile' => $request->mobile,
                    'password' => Hash::make($request->password),
                    'user_type' => "user",
                    'register_by' => "App"
                ]);


                if ($user->save()) {

                    $user->assignRole('user'); // Assign the admin role

                    return $this->ResponseJson('success', 'User Registered Successfully.', null, 201);

                    //For Direct Registration with login
                    /*  $set_request = new Request([
                        'username' => $request->mobile,
                        'password' => $request->password
                    ]);

                    return $this->user_login($set_request); */
                } else {
                    return $this->ResponseJson('error', 'Registration Faild!', null, 422);
                }
            } catch (\Throwable $th) {

                return $this->ResponseJson('error', 'Registration Insert Error', $th->getMessage(), 500);
            }
        }
    }


    public function user_login(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return $this->ResponseJson('error', 'Validation Error.', $validator->messages()->all(), 422);
        }

        // Attempt to authenticate the user with the provided credentials
        $token = $this->guard()->attempt([
            'username' => $request->username,
            'password' => $request->password
        ]);

        // Check if the authentication was successful
        if ($token) {
            // Retrieve authenticated user details
            $user = auth('api')->user();
            $data = $this->getUserData($user);

            // Generate the JWT token
            $data['jwt_token'] = $this->respondWithToken($token);

            // Return success response with user data and token
            return $this->ResponseJson('success', 'Login Successful!', $data, 200);
        } else {
            // Return error response for invalid credentials
            return $this->ResponseJson('error', 'Invalid Credentials.', null, 401);
        }
    }

    private function getUserData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name ?? '',
            'email' => $user->email ?: '',
            'username' => $user->username ?: '',
            'mobile' => $user->mobile ?: '',
            'address' => $user->address ?: '',
            'registered_by' => $user->registered_by,
        ];
    }


    public function userInfo()
    {
        $user = $this->guard()->user();
        $userInfo = $this->getUserData($user);
        return $this->ResponseJson('success', 'User Profile Data', $userInfo, 200);
    }

    public function logout()
    {
        // Log the user out by invalidating the token
        Auth::logout();

        // Return a success response with an appropriate message
        return $this->ResponseJson('success', 'Logout successful', null, 200);
    }


    public function refresh()
    {
        // Refresh the user's token
        $newToken = Auth::refresh();
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Token refreshed successfully',
            'data' => [
                'user_info' => $this->getUserData($user),
                'token' => $newToken,
                'type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60, // Add expiration time in seconds
            ]
        ]);
    }


    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ];
    }
}
