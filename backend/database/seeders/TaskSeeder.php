<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            ['title' => 'Design database schema', 'due_date' => '2026-04-01', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Write API endpoints', 'due_date' => '2026-04-02', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Create unit tests', 'due_date' => '2026-04-03', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Write documentation', 'due_date' => '2026-04-04', 'priority' => 'medium', 'status' => 'done'],
            ['title' => 'Deploy to staging', 'due_date' => '2026-04-05', 'priority' => 'low', 'status' => 'pending'],
            ['title' => 'Performance testing', 'due_date' => '2026-04-06', 'priority' => 'low', 'status' => 'done'],
            ['title' => 'Code review', 'due_date' => '2026-03-30', 'priority' => 'high', 'status' => 'done'],
            ['title' => 'Setup CI/CD', 'due_date' => '2026-03-31', 'priority' => 'medium', 'status' => 'in_progress'],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(
                ['title' => $task['title'], 'due_date' => $task['due_date']],
                $task
            );
        }
    }
}
