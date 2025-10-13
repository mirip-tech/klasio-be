<?php

namespace Database\Factories;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'type' => $this->faker->randomElement(ClassroomType::cases()),
            'grade' => $this->faker->randomElement(ClassroomGrade::cases()),
            'teacher_id' => User::factory()->teacher(),
        ];
    }
}
