<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'school_email' => $this->faker->unique()->safeEmail(),
            'personal_email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * (Optional) Remove this if you're not using email verification.
     */
    // public function unverified(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'email_verified_at' => null,
    //     ]);
    // }
}
