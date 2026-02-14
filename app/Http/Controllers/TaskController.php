<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        // 1. Fetch Active Tasks (Keep using get() here because we want them all)
        $active_tasks = Task::with('dentalOffice')
            ->where('user_id', Auth::user()->id)
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->paginate(20, ['*'], 'active_page');

        // 2. Fetch Past Tasks (Use paginate() instad of get())
        $past_tasks = Task::with('dentalOffice')
            ->where('user_id', Auth::user()->id)
            ->whereIn('status', ['completed', 'converted'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'past_page'); // Added pagination

        return view('tasks.index', compact('active_tasks', 'past_tasks'));
    }

    public function markAsDone(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::find($id);

        if (!$task) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            return redirect()->back()->with('error', 'Task not found');
        }

        // Auth Check
        if ($task->user_id != $user->id && !$user->hasAnyRole(['CountryManager', 'RegionalManager', 'AreaManager'])) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // 1. Update Task Status
        $task->status = 'completed';
        $task->completed_at = now();

        // 2. Save the Note
        if ($request->has('completion_note')) {
            $task->completion_note = $request->input('completion_note');
        }

        // 3. ✅ NEW: Update Dental Office "Receptive" Status
        if ($request->has('receptive_status') && !empty($request->receptive_status)) {
            // Assuming the relationship is 'dentalOffice'
            $task->dentalOffice->update([
                'receptive' => $request->input('receptive_status'),
            ]);
        }

        $task->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Task completed and status updated']);
        }

        return redirect()->back()->with('success', 'Task marked as done');
    }
}
