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
    protected $batchLimit = 50;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Starting Assignment Process with Limit: {$this->batchLimit}...");

        $reps = User::role('SalesRepresentative')->with('states')->get();
        $totalAssigned = 0;
        $batchId = null; // We start with NO batch ID

        foreach ($reps as $rep) {
            // --- A. BUSY CHECK ---
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
            $newLeads = DentalOffice::whereIn('state_id', $allowedStateIds)
                ->whereNull('sales_rep_id')
                ->take($this->batchLimit)
                ->get();

            // Check if we have anything to assign (New or Old) before creating Batch
            $oldOffices = collect();
            if ($newLeads->count() == 0) {
                $oldOffices = DentalOffice::where('sales_rep_id', $rep->id)
                    ->whereIn('state_id', $allowedStateIds)
                    ->whereHas('tasks', function ($q) {
                        $q->whereIn('status', ['completed', 'converted']);
                    })
                    ->whereDoesntHave('tasks', function ($q) {
                        $q->where('status', 'pending');
                    })
                    ->inRandomOrder()
                    ->take($this->batchLimit)
                    ->get();
            }

            // === 🟢 CREATE BATCH ONLY IF WE FOUND WORK ===
            if (($newLeads->count() > 0 || $oldOffices->count() > 0) && $batchId === null) {
                $iteration = new Iteration();
                $iteration->status = 'active';
                $iteration->save();
                $batchId = $iteration->id;
                $this->info("Work found. Created Batch #$batchId");
            }

            // SCENARIO 1: NEW LEADS FOUND
            if ($newLeads->count() > 0) {
                foreach ($newLeads as $lead) {
                    $lead->sales_rep_id = $rep->id;
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
            }
            // SCENARIO 2: OLD TASKS
            elseif ($oldOffices->count() > 0) {
                foreach ($oldOffices as $office) {
                    Task::create([
                        'user_id' => $rep->id,
                        'dental_office_id' => $office->id,
                        'status' => 'pending',
                        'ai_suggested_approach' => 'Follow-up: Re-engaging past client.',
                        'iteration_id' => $batchId,
                    ]);
                    $totalAssigned++;
                }
                $this->info('Recycled ' . $oldOffices->count() . " OLD tasks for {$rep->name}.");
            }
        }

        if ($totalAssigned === 0) {
            $this->info('Batch Complete! No work was assigned, so no new Batch ID was created.');
        } else {
            $this->info("Batch #$batchId Complete! Total Assigned: $totalAssigned");
        }

        return 0;
    }

    private function generateAiStrategy($lead)
    {
        return 'AI Analysis Pending: Pitch standard growth package.';
    }
}
