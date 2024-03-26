<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Nette\NotImplementedException;
use Symfony\Component\HttpFoundation\Response;

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


    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['errors' => ['email' => [__($status)]]], Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)])
            : response()->json(['errors' => ['email' => [__($status)]]], Response::HTTP_BAD_REQUEST);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json();
    }
}
