<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\DentalOffice;
use App\Models\User;
use App\Models\Iteration;
use Carbon\Carbon;

class AutoAssignLeads extends Command
{
    protected $signature = 'leads:auto-assign';
    protected $description = 'Assigns new leads if available, otherwise recycles old tasks.';

    // --- CONFIGURATION ---
    // Change this number to increase/decrease the batch size in the future
    protected $batchLimit = 5;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 1. Create the Batch (Iteration) Record
        $iteration = new Iteration();
        $iteration->status = 'active';
        $iteration->save();
        $batchId = $iteration->id;

        $this->info("Starting Assignment (Batch #$batchId) with Limit: {$this->batchLimit}...");

        $reps = User::role('SalesRepresentative')->with('states')->get();
        $totalAssigned = 0;

        foreach ($reps as $rep) {
            // --- A. BUSY CHECK ---
            // If they have ANY pending tasks, skip them.
            $myPendingCount = Task::where('user_id', $rep->id)->where('status', 'pending')->count();

            if ($myPendingCount > 0) {
                $this->info("Skipping {$rep->name}: They still have $myPendingCount pending tasks.");
                continue;
            }

            // --- B. TERRITORY CHECK ---
            $allowedStateIds = $rep->states->pluck('id')->toArray();
            if (empty($allowedStateIds)) {
                $this->info("Skipping {$rep->name} (No Territory Assigned)");
                continue;
            }

            // --- C. TRY TO FIND NEW LEADS ---
            // We use the full batch limit here.
            $newLeads = DentalOffice::whereIn('state_id', $allowedStateIds)
                ->whereNull('sales_rep_id') // Only unassigned
                ->take($this->batchLimit)
                ->get();

            // === LOGIC BRANCH: NEW VS OLD ===

            if ($newLeads->count() > 0) {
                // SCENARIO 1: NEW LEADS FOUND
                // Assign ONLY the new leads found (even if it's just 1 or 2)
                // We do NOT check for old tasks in this run.

                foreach ($newLeads as $lead) {
                    $lead->sales_rep_id = $rep->id; // Lock to rep
                    $lead->save();

                    Task::create([
                        'user_id' => $rep->id,
                        'dental_office_id' => $lead->id,
                        'status' => 'pending',
                        'ai_suggested_approach' => $this->generateAiStrategy($lead),
                        'iteration_id' => $batchId,
                    ]);
                    $totalAssigned++;
                }
                $this->info('Assigned ' . $newLeads->count() . " NEW leads to {$rep->name}.");
            } else {
                // SCENARIO 2: NO NEW LEADS FOUND -> RECYCLE OLD TASKS
                // Only runs if new leads count is strictly 0

                $oldOffices = DentalOffice::where('sales_rep_id', $rep->id)
                    ->whereIn('state_id', $allowedStateIds)
                    ->whereHas('tasks', function ($q) {
                        $q->whereIn('status', ['completed', 'converted']);
                    })
                    ->whereDoesntHave('tasks', function ($q) {
                        $q->where('status', 'pending');
                    })
                    ->inRandomOrder()
                    ->take($this->batchLimit) // Use full limit
                    ->get();

                if ($oldOffices->count() > 0) {
                    foreach ($oldOffices as $office) {
                        Task::create([
                            'user_id' => $rep->id,
                            'dental_office_id' => $office->id,
                            'status' => 'pending', // Re-open
                            'ai_suggested_approach' => 'Follow-up: Re-engaging past client.',
                            'iteration_id' => $batchId,
                        ]);
                        $totalAssigned++;
                    }
                    $this->info('Recycled ' . $oldOffices->count() . " OLD tasks for {$rep->name}.");
                } else {
                    $this->info("{$rep->name} is free, but has NO new leads and NO recyclable tasks.");
                }
            }
        }

        $this->info("Batch Complete! Total Assigned: $totalAssigned");
        return 0;
    }

    private function generateAiStrategy($lead)
    {
        return 'AI Analysis Pending: Pitch standard growth package.';
    }
}
