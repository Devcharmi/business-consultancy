<?php

namespace Database\Seeders;

use App\Models\StatusManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'color_name' => 'Red'],
            ['name' => 'In Progress', 'color_name' => 'Orange'],
            ['name' => 'Done', 'color_name' => 'Green'],
        ];

        foreach ($statuses as $status) {
            StatusManager::firstOrCreate(
                ['name' => $status['name']], // unique column to check
                ['color_name' => $status['color_name']] // values to insert if not exists
            );
        }
    }
}
