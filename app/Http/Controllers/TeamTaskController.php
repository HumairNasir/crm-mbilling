<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reps = collect();

        // 1. FETCH SALES REPS (Hierarchy Logic)
        if ($user->hasRole('CountryManager')) {
            $reps = User::role('SalesRepresentative')->get();
        } elseif ($user->hasRole('RegionalManager')) {
            $regionIds = $user->regions->pluck('id');
            $stateIds = State::whereIn('region_id', $regionIds)->pluck('id');
            $reps = User::role('SalesRepresentative')
                ->whereHas('states', function ($q) use ($stateIds) {
                    $q->whereIn('states.id', $stateIds);
                })
                ->get();
        } elseif ($user->hasRole('AreaManager')) {
            $stateIds = $user->states->pluck('id');
            $reps = User::role('SalesRepresentative')
                ->whereHas('states', function ($q) use ($stateIds) {
                    $q->whereIn('states.id', $stateIds);
                })
                ->get();
        }

        return view('team_tasks', compact('reps'));
    }

    public function fetchRepTasks($rep_id)
    {
        $rep = User::find($rep_id);
        if (!$rep) {
            return response()->json(['error' => 'Rep not found'], 404);
        }

        // Fetch ALL Active tasks (For scrollable list)
        $active_tasks = Task::with(['dentalOffice.state', 'dentalOffice.region'])
            ->where('user_id', $rep_id)
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'active_page');

        // Fetch Last 100 Past tasks
        $past_tasks = Task::with(['dentalOffice.state', 'dentalOffice.region'])
            ->where('user_id', $rep_id)
            ->whereIn('status', ['completed', 'converted'])
            ->latest()
            ->paginate(10, ['*'], 'past_page');

        // Render the Partial View
        return view('partials.rep_task_list', compact('active_tasks', 'past_tasks', 'rep'))->render();
    }
}
