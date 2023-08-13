<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    private function storeFile($file, $taskTitle)
    {
        $uniqueFilename = $taskTitle . '_' . time() . '_' . $file->getClientOriginalName();
        $file->storeAs('attachments', $uniqueFilename, 'public');

        return $uniqueFilename;
    }

    public function create(Request $request)
    {
        try {
            $user = auth()->user();
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'completed' => 'boolean',
            ]);

            $task = new Task($validatedData);
            $task->user_id = $user->id;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = $this->storeFile($file, $validatedData['title']);
                $task->attachment = $filename;
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $task->save();
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function findOne(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json(['tasks' => $task], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        }
    }

    public function findAll(Request $request)
    {
        $tasks = Task::all();
        return response()->json(['tasks' => $tasks], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'completed' => 'boolean',
            ]);

            $task->fill($validatedData);

            if ($request->hasFile('attachment')) {
                if ($task->attachment) {
                    Storage::disk('public')->delete('attachments/' . $task->attachment);
                }

                $file = $request->file('attachment');
                $filename = $this->storeFile($file, $request->input('title') ?? $task->title);
                $task->attachment = $filename;
            }
            $task->updated_at = time();

            $task->save();
            return response()->json(['message' => "Updated task with id: $id"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if ($task->attachment) {
                Storage::disk('public')->delete('attachments/' . $task->attachment);
            }

            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        }
    }
}
