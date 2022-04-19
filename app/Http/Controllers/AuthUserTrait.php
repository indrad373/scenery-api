<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

trait AuthUserTrait
{
    private function getAuthUser()
    {
        try {
            return auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            response()->json(['message' => 'Unauthorized, anda harus login terlebih dahulu'])->send();
            exit;
        }
    }
}