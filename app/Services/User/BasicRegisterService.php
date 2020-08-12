<?php

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\User\Contracts\RegisterService;
use App\User;
use Illuminate\Support\Facades\Validator;

class BasicRegisterService implements RegisterService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createAccount(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function isRegistered(?string $email): bool
    {
        return !is_null($this->userRepository->findByEmail($email));
    }

    public function isCorrectRegisterData(array $data): bool
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        return !$validator->fails();
    }
}