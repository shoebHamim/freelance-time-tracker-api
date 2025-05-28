<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();

        Project::create([
            'title' => 'Website Redesign',
            'description' => 'Redesign the company website.',
            'client_id' => $clients[0]->id,
            'status' => 'active',
            'deadline' => now()->addDays(30),
        ]);

        Project::create([
            'title' => 'Mobile App',
            'description' => 'Develop a mobile app for Beta LLC.',
            'client_id' => $clients[1]->id,
            'status' => 'active',
            'deadline' => now()->addDays(60),
        ]);
    }
}
