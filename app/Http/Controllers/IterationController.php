<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DentalOffice;
use App\Models\Task;
use App\Models\Iteration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IterationController extends Controller
{
    public function store(Request $request)
    {
        // 1. Create or Get Active Iteration (Batch Log)
        $activeIteration = Iteration::where('status', 'active')->first();
        if (!$activeIteration) {
            $iteration = new Iteration();
            $iteration->status = 'active';
            $iteration->start_time = Carbon::now();
            $iteration->save();
        } else {
            $iteration = $activeIteration;
        }

        // 2. Get All Active Sales Reps
        $salesReps = User::role('SalesRepresentative')->with('states')->get();

        $report = [];
        $totalAssigned = 0;
        $batchSize = 5; // Configurable: Tasks per rep per batch

        DB::beginTransaction(); // Start Transaction

        try {
            foreach ($salesReps as $rep) {
                // A. Identify Territory
                $allowedStateIds = $rep->states->pluck('id')->toArray();

                // Skip if no territory
                if (empty($allowedStateIds)) {
                    $report[] = "{$rep->name}: Skipped (No Territory)";
                    continue;
                }

                // =========================================================
                // LOGIC CHANGE 1: CHECK AVAILABILITY (Busy Check)
                // =========================================================
                // If the Rep has ANY pending tasks, do not assign more.
                $busyCheck = Task::where('user_id', $rep->id)->where('status', 'pending')->count();

                if ($busyCheck > 0) {
                    $report[] = "{$rep->name}: Skipped (Busy with $busyCheck tasks)";
                    continue; // Stop here for this Rep
                }

                // =========================================================
                // LOGIC CHANGE 2: PRIORITY ASSIGNMENT (New vs Recycled)
                // =========================================================

                // PRIORITY A: Fresh Leads (Unassigned) in Territory
                $leads = DentalOffice::whereIn('state_id', $allowedStateIds)
                    ->whereNull('sales_rep_id') // Strictly new leads
                    ->take($batchSize)
                    ->get();

                $source = 'New';

                // PRIORITY B: Recycle Old Leads (If no new leads found)
                if ($leads->isEmpty()) {
                    $source = 'Recycled';

                    // Logic: Find oldest 'completed' tasks for this rep in their territory
                    // We fetch the DentalOffice models associated with those tasks
                    $leads = DentalOffice::whereIn('state_id', $allowedStateIds)
                        ->whereHas('tasks', function ($q) use ($rep) {
                            $q->where('user_id', $rep->id)->where('status', 'completed');
                        })
                        // Ensure we don't pick one that is already currently pending (double safety)
                        ->whereDoesntHave('tasks', function ($q) {
                            $q->where('status', 'pending');
                        })
                        ->with([
                            'tasks' => function ($q) {
                                $q->latest(); // To check last interaction time if needed
                            },
                        ])
                        ->take($batchSize)
                        ->get();
                }

                // --- Logging for Audit ---
                if ($rep->id == 61) {
                    Log::info("AUTO-PILOT: Rep {$rep->name} - Source: {$source} - Count: {$leads->count()}");
                }

                $countForThisRep = 0;

                // C. Process Assignment
                foreach ($leads as $lead) {
                    // 1. Ensure Lead is Locked to Rep (Vital for Recycled leads too)
                    if ($lead->sales_rep_id !== $rep->id) {
                        $lead->sales_rep_id = $rep->id;
                        $lead->save();
                    }

                    // 2. Create a NEW Task
                    // We create a NEW row even for recycling to preserve the history/notes of the old task.
                    Task::create([
                        'user_id' => $rep->id,
                        'dental_office_id' => $lead->id,
                        'status' => 'pending',
                        'iteration_id' => $iteration->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        // Optional: Add a system note to differentiate recycled tasks?
                        // 'completion_note' => ($source === 'Recycled') ? 'System: Recycled Lead' : null
                    ]);

                    $countForThisRep++;
                    $totalAssigned++;
                }

                // D. Report Generation
                if ($countForThisRep == 0) {
                    $report[] = "{$rep->name}: No leads available (New or Recycled)";
                } else {
                    $report[] = "{$rep->name}: Assigned {$countForThisRep} ({$source})";
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Batch completed. Total Assigned: $totalAssigned",
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
