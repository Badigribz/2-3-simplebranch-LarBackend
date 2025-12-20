<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;

class RootPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Person::firstOrCreate([
            'name' => 'Mother',
            'parent_id' => null,
        ]);
    }
}
