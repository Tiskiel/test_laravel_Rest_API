<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function inscription(Request $request) {
        $utilisateurDonnee = $request->validate([
            "name" => ["required", "string", "min:2", "max:255"],
            "email" => ["required", "email", "unique:users,email"],
            "password" => ["required", "string", "min:8", "max:30", "confirmed"]
        ]);

        $utilisateur = User::create([
            "name" => $utilisateurDonnee["name"],
            "email" => $utilisateurDonnee["email"],
            "password" => bcrypt($utilisateurDonnee["password"])
        ]);

        return response($utilisateur, 201);
    }

    public function connection(Request $request) {
        $utilisateurDonnee = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string", "min:8", "max:30"]
        ]);
        $user = User::where("email", $utilisateurDonnee["email"])->first();

        if(!$user) return response(["message" => "Key value not good "], 401);

        if(!Hash::check($utilisateurDonnee["password"], $user->password)) return response(["message" => "Key value not good PW "], 401);

        $token = $user->createToken("CLE_SECRETE")->plainTextToken;

        return response([
            "utilisateur" => $user,
            "token" => $token
        ], 200);
    }
}
