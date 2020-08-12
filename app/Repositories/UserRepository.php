<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(array $data): User
    {
        return $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }
}