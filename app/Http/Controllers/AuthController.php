<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Nothing to construct
    }

    // Register
    public function register(Request $request)
    {
        // Validating request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response(false, $validator->errors(), null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $register = User::create($request->all());

            if ($register) {
                $user = User::where('email', $request->email)->first();
                $jwt = $this->jwt($user);
                return $this->response(true, 'success register', ['token' => $jwt], Response::HTTP_CREATED);
            } else {
                return $this->response(false, 'failed register', null, Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return $this->response(false, "internal server error at register func", null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        // Validating request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response(false, $validator->errors(), null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $user = User::where('email', $email)->first();

            if ($user) {
                if ($user->checkPassword($password)) {
                    $jwt = $this->jwt($user);
                    return $this->response(true, 'success login', ['token' => $jwt], Response::HTTP_OK);
                } else {
                    return $this->response(false, 'credential does not match', null, Response::HTTP_BAD_REQUEST);
                }
            } else {
                return $this->response(false, 'user not found', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, "internal server error at login func", null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Helper function for jwt
    private function jwt(User $user)
    {
        $payload = [
            "sub" => $user->email,
            'iss' => 'lumen-jwt-praktikum-final-server',
            'aud' => 'lumen-jwt-praktikum-final-client',
            'iat' => time(),
            'exp' => time() * 60 * 60,
            'role' => $user->role,
        ];

        return JWT::encode($payload, env('JWT_KEY', 'secret'), 'HS256');
    }
}
