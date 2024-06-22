<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PackageSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(ColorsSeeder::class);
        $this->call(ProjectsSeeder::class);
        $this->call(FormsSeeder::class);
        $this->call(RecipientsSeeder::class);
        $this->call(SubmissionsSeeder::class);
        $this->call(TagsSeeder::class);
    }
}
