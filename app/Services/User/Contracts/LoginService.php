<?php

namespace App\Services\User\Contracts;

use Illuminate\Http\Request;

interface LoginService
{
    public function validateLogin(Request $request): bool;

    public function attemptLogin(Request $request): bool;

    public function sendLoginResponse(Request $request);

    public function sendFailedLoginResponse(Request $request);

    public function setIsNewbie(bool $isNewbie): bool;
}