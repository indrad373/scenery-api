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

    private function checkOwnership($owner)
    {
        //usernya kita dapetin dari getAuthUser func
        $user = $this->getAuthUser();

        if($user->id != $owner)
        {
            //ketika id di table forum berbeda dengan id pembuat data nya maka kita akan return 403 response
            response()->json(['message' => 'Not Authorized'], 403)->send();
            exit;
        }
    }
}