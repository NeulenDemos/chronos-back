<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }
    public function register(Request $request)
    {
        $data = $request->all();
        $result = User::where(['email' => $data['email']])->get()->all();
        if ($result)
            return response()->json(['error' => 'The email has already been taken'], 422);
        $result = User::where(['login' => $data['login']])->get()->all();
        if ($result)
            return response()->json(['error' => 'The username has already been taken'], 422);
        $data['password'] = Hash::make($data['password']);
        $result = User::create($data);
        return $result;
    }
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials))
            return response()->json(['error' => 'Unauthorized'], 401);
        return $this->respondWithToken($token);
    }
    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function resetPassword(Request $request)
    {
        $token = substr(bin2hex(random_bytes(10)), 0, 10);
        $email = $request->all()['email'];
        $result = User::where(['email' => $email])->get()->all();
        if (!$result)
            return 0;
        $login = $result[0]['name'];
        ResetPassword::create(["email" => $email, "token" => $token]);
        MailController::sendResetPassword($email, $login, $token);
        return 1;
    }
    public function newPassword($token, Request $request)
    {
        $query = ResetPassword::where(['token' => $token]);
        $result = $query->get()->all();
        if (!$result)
            return 0;
        $query->delete();
        $email = $result[0]['email'];
        $password = Hash::make($request->all()["password"]);
        $result = User::where(['email' => $email])->update(["password" => $password]);
        return $result;
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
