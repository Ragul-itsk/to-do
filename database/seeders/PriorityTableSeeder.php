<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('priorities')->delete();

        $priority = array(
            array('name' => 'High', 'code' => 'HP', 'created_by' => 1, 'updated_by' => 1),
            array('name' => 'Middle', 'code' => 'MP', 'created_by' => 1, 'updated_by' => 1),
            array('name' => 'Low', 'code' => 'LP', 'created_by' => 1, 'updated_by' => 1),
        );

        DB::table('priorities')->insert($priority);
    }
}
