<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'priority' => Priority::class,
            'status' => Status::class,
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw("
            CASE priority
                WHEN 'high' THEN 3
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 1
            END DESC
        ")->orderBy('due_date', 'asc');
    }
}
