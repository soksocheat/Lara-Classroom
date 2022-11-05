<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Shift;
use App\Models\Student;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create();

        $departments = ['Information Technoloy', 'Business Administration', 'Economics', 'Law', 'Education, Arts and Humanities', 'Engineering'];
        foreach ($departments as $name) {   
            Department::create([
                'name' => $name
            ])->each(function (Department $d) {
                Lecturer::factory(2)->create([
                    'department_id' => $d->id
                ]);
            });
        }

        Student::factory(20)->create();

        $shifts = ['Evening', 'Afternoon', 'Morning'];
        foreach ($shifts as $name) {  
            Shift::create([
                'name' => $name
            ]);
        }
    }
}
