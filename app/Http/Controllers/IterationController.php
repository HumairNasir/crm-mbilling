<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DentalOffice;
use App\Models\Task;
use App\Models\Iteration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IterationController extends Controller
{
    public function store(Request $request)
    {
        // 1. Create a New Iteration Record (Batch Log)
        // Check if an active one exists first to avoid duplicates if you want
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
        // We eagerly load 'states' to limit database queries
        $salesReps = User::role('SalesRepresentative')->with('states')->get();

        $report = []; // To store who got what
        $totalAssigned = 0;
        $batchSize = 5; // Configurable: How many leads per rep per hour

        DB::beginTransaction(); // Start Transaction for Safety

        try {
            foreach ($salesReps as $rep) {
                // A. Identify Territory
                $allowedStateIds = $rep->states->pluck('id')->toArray();

                // --- LOGGING START ---
                if ($rep->id == 61) {
                    // Only log for Rep 01 to keep file clean
                    \Illuminate\Support\Facades\Log::info('AUTO-PILOT AUDIT: Rep 01 (ID 61)');
                    \Illuminate\Support\Facades\Log::info('Allowed States found: ' . implode(',', $allowedStateIds));
                }

                // If Rep has no territory, skip them
                if (empty($allowedStateIds)) {
                    $report[] = "{$rep->name}: Skipped (No Territory Assigned)";
                    continue;
                }

                // B. Find Available Leads in Their Territory
                // Logic: Must be in their state AND not yet assigned to anyone
                $leads = DentalOffice::whereIn('state_id', $allowedStateIds)
                    ->whereNull('sales_rep_id') // Only fresh leads
                    ->take($batchSize)
                    ->get();

                // --- LOGGING START ---
                if ($rep->id == 61 && $leads->count() > 0) {
                    foreach ($leads as $l) {
                        \Illuminate\Support\Facades\Log::info(
                            ">> ASSIGNING LEAD: {$l->name} (ID: {$l->id}) - State ID: {$l->state_id}",
                        );
                    }
                }

                $countForThisRep = 0;

                // C. Assign Leads & Create Tasks
                foreach ($leads as $lead) {
                    // 1. Lock the lead to this Rep
                    $lead->sales_rep_id = $rep->id;
                    $lead->save();

                    // 2. Create the Task
                    Task::create([
                        'user_id' => $rep->id,
                        'dental_office_id' => $lead->id,
                        'status' => 'pending',
                        'iteration_id' => $iteration->id, // Link to this batch
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $countForThisRep++;
                    $totalAssigned++;
                }

                // D. Log the result for this Rep
                if ($countForThisRep < $batchSize) {
                    $report[] = "{$rep->name}: Assigned {$countForThisRep} (Territory Dry)";
                } else {
                    $report[] = "{$rep->name}: Assigned {$countForThisRep} (Full Batch)";
                }
            }

            DB::commit(); // Save all changes

            // 3. Return Success with the Report
            return response()->json([
                'status' => 'success',
                'message' => "Batch completed. Total Assigned: $totalAssigned",
                'report' => $report, // This shows you the imbalance details!
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Undo changes if something crashes
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
