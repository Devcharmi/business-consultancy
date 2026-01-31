<?php

namespace Database\Seeders;

use App\Models\PriorityManager;
use App\Models\StatusManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriorityManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            ['name' => 'Low', 'color_name' => '#6c757d'],    // Gray — low attention
            ['name' => 'Medium', 'color_name' => '#17a2b8'], // Teal — moderate
            ['name' => 'High', 'color_name' => '#ffc107'],   // Amber — important
            ['name' => 'ASAP', 'color_name' => '#dc3545'],   // Bright red — urgent
        ];

        foreach ($priorities as $priority) {
            PriorityManager::firstOrCreate(
                ['name' => $priority['name']], // unique column to check
                ['color_name' => $priority['color_name']] // values to insert if not exists
            );
        }
    }
}
