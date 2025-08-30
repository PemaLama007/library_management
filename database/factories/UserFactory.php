<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'pemawoser',
            'username' => 'pemawoser',
            'password' => '$2y$10$Z1sJY9jANJnCKWcwgb4yPeyMXTAA7k4OF5633Quizer34mPWfsZ1.', // admin
        ];
    }


}
