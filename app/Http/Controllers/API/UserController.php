<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;
class UserController extends Controller
{

    #[OA\Post(
        path: "/register",
        summary: "Inscription",
        description: "Crée un nouvel utilisateur.",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RegisterRequest")
        ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: "Utilisateur Créé",
                ),
                new OA\Response(
                    response: 422,
                    description: "Validation Error",
                )
            ]
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'l\'utilisateur a bien été créé', 'user' => $user], 201);
    }
    #[OA\Post(
        path: "/login",
        summary: "Connexion",
        description: "Connecte un utilisateur existant.",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LoginRequest")
        ),
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Connexion réussie",
                ),
                new OA\Response(
                    response: 401,
                    description: "Identifiants invalides",
                )
            ]
    )]
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }
    #[OA\Post(
        path: "/logout",
        summary: "Déconnexion",
        description: "Déconnecte l'utilisateur courant.",
        tags: ["Auth"],
            responses: [
                new OA\Response(
                    response: 204,
                    description: "Déconnexion réussie",
                )
            ]
    )]
    #[OA\HeaderParameter(name: "Accept", required: true, schema: new OA\Schema(type: "string", example: "application/json"))]
    #[OA\HeaderParameter(name: "Authorization", required: true, schema: new OA\Schema(type: "string", example: "Bearer 1|123....token...aBC"))]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
