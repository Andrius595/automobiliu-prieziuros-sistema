<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Config\PermissionsConfig;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nette\NotImplementedException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = auth()->attempt([
            'email' => $request->input('email'),
            'password' => $request->get('password')
        ]);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token]);
    }

    public function refresh()
    {
        $token = auth()->refresh();

        if (!$token) {
            return response()->json(['error' => 'Refresh token already expired'], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    public function register(Request $request, CreateNewUser $createNewUser): JsonResponse
    {
        $user = $createNewUser->create($request->all());

        $user->assignRole(PermissionsConfig::CLIENT_ROLE);

        event(new Registered($user));

        return response()->json($user);
    }


    public function forgotPassword()
    {
        throw new NotImplementedException();
    }

    public function resetPassword()
    {
        throw new NotImplementedException();
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json();
    }
}
