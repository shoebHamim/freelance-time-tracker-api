<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeLog;
use App\Models\Project;
use Carbon\Carbon;

class TimeLogSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        for ($i = 0; $i < 3; $i++) {
            $start = Carbon::now()->subDays(5 - $i)->setTime(9, 0);
            $end = (clone $start)->addHours(3);
            TimeLog::create([
                'project_id' => $projects[0]->id,
                'start_time' => $start,
                'end_time' => $end,
                'hours' => $end->diffInHours($start,true),
                'description' => "Work session " . ($i + 1) . " for Website Redesign",
            ]);
        }

        for ($i = 0; $i < 6; $i++) {
            $start = Carbon::now()->subDays(2 - $i)->setTime(10, 0);
            $end = (clone $start)->addHours(4);
            TimeLog::create([
                'project_id' => $projects[1]->id,
                'start_time' => $start,
                'end_time' => $end,
                'hours' => $end->diffInHours($start,true),
                'description' => "Work session " . ($i + 1) . " for Mobile App",
            ]);
        }
    }
}
