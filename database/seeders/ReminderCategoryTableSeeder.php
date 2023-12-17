<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReminderCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reminder_categories')->delete();

        $reminderCategory = array(
            array('user_id' => 1, 'name' => 'Uncategories')
        );

        DB::table('reminder_categories')->insert($reminderCategory);
    }
}
