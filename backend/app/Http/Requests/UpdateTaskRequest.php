<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('due_date', $this->input('due_date', $this->task?->due_date));
                })->ignore($this->task),
            ],
            'due_date' => [
                'sometimes',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'medium', 'high']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'A task with this title already exists for the given due date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'priority.in' => 'Priority must be low, medium, or high.',
        ];
    }
}
