<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UsersController
{
    public function register(UserRequest $request) {
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();

        $token = $user->createToken('auth', ['user'])->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        // Validation des données, email et mot de passe sont obligatoires et l'email doit être un email
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Authentification du user, `attempt` permet de vérifier les identifiants du user
        // Si ils sont incorrects, une erreur 401 est retournée (Unauthorized)
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Récupération du user authentifié (avec le auth()->attempt, le user est authentifié)
        $user = auth()->user();

        // Création d'un token pour le user (sanctum, le token sera stocké en base de données et sera utilisé pour authentifier le user dans les prochaines requêtes)
        // via un Bearer Token
        $token = $user->createToken('auth', ['user'])->plainTextToken;

        // Retourne une réponse JSON avec le user et le token
        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        // Même réponse que l'email existe ou non : on ne révèle pas quels comptes existent
        return response(['message' => 'Un lien viens de vous être envoyé.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // exige password_confirmation
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // déconnecte toutes les sessions existantes
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response(['message' => 'Lien invalide ou expiré.'], 422);
        }

        return response(['message' => 'Votre mot de passe a bien été réinitialisé.']);
    }
}
