<?php

namespace App\Http\Resources;

use App\Enums\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $allowedNextStatus = $this->status->allowedTransitions();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'due_date' => $this->due_date->format('Y-m-d'),
            'priority' => $this->priority->value,
            'status' => $this->status->value,
            'allowed_next_status' => array_map(fn (Status $s) => $s->value, $allowedNextStatus),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
