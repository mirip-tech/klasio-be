<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = User::factory(5)->teacher()->create();
        $students = User::factory(50)->student()->create();

        $studentsPerClass = ceil($students->count() / $teachers->count());
        $studentGroups = $students->chunk($studentsPerClass);

        foreach ($teachers as $index => $teacher) {
            $classroom = Classroom::factory()->create([
                'teacher_id' => $teacher->id,
            ]);

            $group = $studentGroups[$index] ?? collect();
            $classroom->students()->attach($group->pluck('id')->toArray());

            // log ke console
            $this->command->info("Classroom {$classroom->id} ({$teacher->name}) -> {$group->count()} students");
        }
    }
}
