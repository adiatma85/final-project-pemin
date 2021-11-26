<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\ResponseTrait;

class UserController extends Controller
{

    use ResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // TODO: Create user logic

    // To get All user
    public function index()
    {
        try {
            $users = User::all();
            return $this->response(true, 'success fetching resources', compact('users'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at index user func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // To get particular user
    public function getById(Request $request, $userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                if ($request->user->hasRole('admin') || $request->user->isSamePerson($userId)) {
                    return $this->response(true, 'success to fetch praticular resource', compact('user'), Response::HTTP_OK);
                } else {
                    return $this->response(false, 'forbidden request', null, Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->response(false, 'failed to search particular resource', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at show user func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // To update particular user
    public function update(Request $request, $userId)
    {
        try {
            $user = User::where('id', $userId);
            if ($user->exists()) {
                if ($request->user->isSamePerson($userId)) {
                    $user->update($request->all());
                    return $this->response(true, 'success to update a resource', ['user' => $user->first()], Response::HTTP_OK);
                } else {
                    return $this->response(false, 'forbidden request', null, Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->response(false, 'particular resopnse does not found', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at update user func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // To delete particular user
    public function delete(Request $request, $userId)
    {
        try {
            $user = User::where('id', $userId);
            if ($user->exists()) {
                if ($request->user->isSamePerson($userId)) {
                    $user->delete();
                    return $this->response(true, 'success to delete a resource', null, Response::HTTP_OK);
                } 
                // else if ($request->user->hasRole('admin')) {
                //     $user->delete();
                //     return $this->response(true, 'success to delete a resource', null, Response::HTTP_OK);
                // } 
                else {
                    return $this->response(false, 'forbidden request', null, Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->response(false, 'particular resource does not found', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at delete user func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
