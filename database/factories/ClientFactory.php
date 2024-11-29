<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'type' => $this->faker->randomElement(['business', 'residential']), // Random type
        ];
    }

    /**
     * State for business clients.
     */
    public function business()
    {
        return $this->state([
            'type' => 'business',
        ]);
    }

    /**
     * State for residential clients.
     */
    public function residential()
    {
        return $this->state([
            'type' => 'residential',
        ]);
    }
}