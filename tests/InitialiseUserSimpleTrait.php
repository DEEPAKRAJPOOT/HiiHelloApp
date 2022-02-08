<?php

namespace Tests;

use App\Models\User;

trait InitialiseUserSimpleTrait
{
    protected function setAuthenticatedToken($userType)
    {
        $faker = \Faker\Factory::create('en_UK');

        $user = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456789'),
        ];

        $this->user = User::create($user);

        $this->token = $this->user->createToken($userType)->plainTextToken;
    }
}
