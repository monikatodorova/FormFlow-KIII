<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubmissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Submission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateTime = $this->faker->dateTimeBetween('-14 days');
        return [
            'form_id' => $this->faker->numberBetween(1, 200),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'fields' => [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'phone' => $this->faker->phoneNumber,
                'city' => $this->faker->city,
                'country' => $this->faker->country,
            ],
            'status' => $this->faker->randomElement(Submission::STATUS_OPTIONS),
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
    }
}
