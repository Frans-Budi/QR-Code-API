<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);
        $token = $user->createToken(User::USER_TOKEN);

        return $this->success(
            [
                "user" => $user,
                "token" => $token->plainTextToken,
            ],
            "User berhasil Register"
        );
    }

    public function login(LoginRequest $request)
    {
        $isValid = $this->isValidCredential($request);

        if (!$isValid["success"]) {
            return $this->error(
                $isValid["message"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = $isValid["user"];
        $token = $user->createToken(User::USER_TOKEN);

        return $this->success(
            [
                "user" => $user,
                "token" => $token->plainTextToken,
            ],
            "Login berhasil!"
        );
    }

    private function isValidCredential(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where("email", $data["email"])->first();
        if ($user == null) {
            return [
                "success" => false,
                "message" => "Email tidak ditemukan",
            ];
        }

        if (Hash::check($data["password"], $user->password)) {
            return [
                "success" => true,
                "user" => $user,
            ];
        }

        return [
            "success" => false,
            "message" => "Password yang Anda masukkan salah",
        ];
    }

    public function loginWithToken()
    {
        return $this->success(auth()->user(), "Login Berhasil");
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return $this->success(null, "Logout Berhasil");
    }
}
