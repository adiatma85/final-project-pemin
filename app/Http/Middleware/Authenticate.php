<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Traits\ResponseTrait;
use App\Models\User;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authenticate
{

    use ResponseTrait;

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return $this->response(false, 'token is not provided', null, Response::HTTP_UNAUTHORIZED);
        }

        try {
            $jwt = explode(' ', $token)[1]; //Bearer
            $credentials = JWT::decode($jwt, new Key(env('JWT_KEY', 'secret'), 'HS256'));
        } catch (ExpiredException $th) {
            return $this->response(false, 'token is expired', null, Response::HTTP_UNAUTHORIZED);
        } catch (Exception $e) {
            return $this->response(false, 'internal server error at auth middleware func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = User::where('email', $credentials->sub)->first();
        if ($role) {
            $condition = $user->hasRole($role);
            if (!$condition) {
                return $this->response(false, 'forbidden', null, Response::HTTP_FORBIDDEN);
            }
        }

        $request->user = $user;
        return $next($request);
    }
}
