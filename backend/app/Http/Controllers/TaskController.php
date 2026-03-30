<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $query = Task::ordered();

        if ($request->has('status')) {
            $status = Status::from($request->query('status'));
            $query->where('status', $status->value);
        }

        $tasks = $query->paginate($request->integer('per_page', 15));

        if ($tasks->isEmpty()) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => $tasks->currentPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'last_page' => $tasks->lastPage(),
                ],
                'message' => 'No tasks found.',
            ]);
        }

        return TaskResource::collection($tasks)->additional([
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_page' => $tasks->lastPage(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = Status::PENDING->value;
        $task = Task::create($data);

        return response()->json([
            'data' => new TaskResource($task),
        ], 201);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'data' => new TaskResource($task->fresh()),
        ]);
    }

    public function updateStatus(UpdateStatusRequest $request, Task $task): JsonResponse
    {
        $targetStatus = Status::from($request->validated('status'));
        $currentStatus = $task->status;

        if (!in_array($targetStatus, $currentStatus->allowedTransitions())) {
            Log::warning('Invalid status transition attempted', [
                'task_id' => $task->id,
                'from_status' => $currentStatus->value,
                'to_status' => $targetStatus->value,
            ]);

            return response()->json([
                'message' => 'Invalid status transition.',
                'error' => 'INVALID_TRANSITION',
                'context' => [
                    'current_status' => $currentStatus->value,
                    'attempted_status' => $targetStatus->value,
                    'allowed_next_status' => array_map(fn ($s) => $s->value, $currentStatus->allowedTransitions()),
                ],
            ], 422);
        }

        $task->update(['status' => $targetStatus]);

        return response()->json([
            'data' => new TaskResource($task->fresh()),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->status !== Status::DONE) {
            return response()->json([
                'message' => "Only tasks with status 'done' can be deleted.",
                'error' => 'DELETE_FORBIDDEN',
                'context' => [
                    'task_id' => $task->id,
                    'current_status' => $task->status->value,
                ],
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function report(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->query('date');

        $counts = DB::table('tasks')
            ->where('due_date', $date)
            ->selectRaw("
                priority,
                status,
                COUNT(*) as count
            ")
            ->groupBy('priority', 'status')
            ->get()
            ->keyBy(fn ($row) => "{$row->priority}.{$row->status}");

        $summary = [];
        foreach (Priority::cases() as $priority) {
            $summary[$priority->value] = [];
            foreach (Status::cases() as $status) {
                $key = "{$priority->value}.{$status->value}";
                $summary[$priority->value][$status->value] = (int) ($counts[$key]->count ?? 0);
            }
        }

        return response()->json([
            'date' => $date,
            'summary' => $summary,
        ]);
    }
}
