<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;
use App\Data\User\UserLoginRequest;
use App\Data\User\UserResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class AuthenticationController extends Controller
{
    const route = 'auth';

    public function login(UserLoginRequest $req)
    {
        if (!Auth::attempt($req->only('nrp', 'password')->toArray())) {
            // return $this->error($req->toArray(), Response::HTTP_UNAUTHORIZED, 'TODO Credentials do not match');
            return $this->error([], Response::HTTP_UNAUTHORIZED, 'TODO Credentials do not match');
        }

        /** @var App\Models\User | null */
        $user = User::all()->where('nrp', $req->nrp)->first();

        /** @var NewAccessToken */
        $token = $user->createToken('Token ' . $user->nrp, [], Carbon::now()->addDays(14));

        (array) $data = UserResponse::from($user)->toArray();

        return $this->successAuth($data, $token, Response::HTTP_OK);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return $this->success([], Response::HTTP_OK, 'You have successfully been logged out and your token has been deleted');
    }

    public function currentUser(Request $req)
    {
        /** @var App\Models\User */
        $user = $req->user();
        (array) $data = UserResponse::from($user)->toArray();
        return $this->success($data, Response::HTTP_OK, null);
    }
}
