<?php

namespace Larapress\Profiles\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Larapress\Profiles\Models\PhoneNumber;

class PhoneNumberFactory extends Factory {
    protected $model = PhoneNumber::class;

    public function definition()
    {
        return [
            'number' => $this->faker->phoneNumber,
            'flags' => PhoneNumber::FLAGS_VERIFIED,
        ];
    }
}
