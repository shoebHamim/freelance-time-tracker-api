<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Client::create([
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'contact_person' => 'Alice',
            'user_id' => $user->id,
        ]);

        Client::create([
            'name' => 'Beta LLC',
            'email' => 'beta@example.com',
            'contact_person' => 'Bob',
            'user_id' => $user->id,
        ]);
    }
}
