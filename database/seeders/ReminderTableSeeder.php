<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReminderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reminders')->delete();
        $currentDate = Carbon::now();
        $reminder = array(
            array(
                'reminder_category_id' => 1,
                'priority_id' => 1,
                'title' => 'Exhibit World Live',
                'description' => 'Sample Description',
                'type' => 'daily',
                'due_date' => $currentDate,
            )
        );

        DB::table('reminders')->insert($reminder);
    }
}
