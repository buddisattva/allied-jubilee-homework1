<?php

namespace App\Services\User\Contracts;

use Illuminate\Http\Request;

interface RegisterService
{
    public function createAccount(array $data);

    public function isRegistered(?string $email): bool;

    public function isCorrectRegisterData(array $data): bool;
}