<?php

namespace Larapress\Profiles\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Larapress\Profiles\Models\Domain;

class DomainFactory extends Factory {
    protected $model = Domain::class;

    public function definition()
    {
        $title = $this->faker->words(1, true);
        return [
            'domain' => str_replace(' ', '-', strtolower($title)),
            'ips' => '127.0.0.1',
            'flags' => Domain::FLAG_DEFAULT_DOMAIN,
            'data' => [
                'gateway' => $title,
            ],
        ];
    }
}
