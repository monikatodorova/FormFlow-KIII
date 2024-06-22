<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            'name' => 'Standard Package',
            'description' => 'For personal or portfolio sites',
            'projects' => 20,
            'submissions' => 10000,
            'archive' => 180,
            'price' => 0,
        ]);
    }
}
