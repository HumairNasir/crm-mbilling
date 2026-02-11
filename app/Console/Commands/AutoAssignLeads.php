<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\DentalOffice;
use App\Models\User;
use App\Models\Iteration; // <--- 1. IMPORT THIS
use Carbon\Carbon;

class AutoAssignLeads extends Command
{
    protected $signature = 'leads:auto-assign';
    protected $description = 'Automatically assigns new leads to sales reps.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 1. Create the Iteration Record
        $iteration = new Iteration();
        $iteration->status = 'active';
        $iteration->save();
        $batchId = $iteration->id;

        $this->info("Starting Territory-Based Assignment (Batch #$batchId)...");

        // 2. Get All Reps with their States
        $reps = User::role('SalesRepresentative')->with('states')->get();
        $totalAssigned = 0;

        foreach ($reps as $rep) {
            // --- NEW: THE BUSY CHECK ---
            // Check if THIS specific Rep has any pending tasks
            $myPendingCount = Task::where('user_id', $rep->id)->where('status', 'pending')->count();

            if ($myPendingCount > 0) {
                $this->info("Skipping {$rep->name}: They still have $myPendingCount pending tasks.");
                continue; // STOP here for this Rep, and loop to the next person immediately
            }
            // ---------------------------

            // A. Get the State IDs this Rep owns
            $allowedStateIds = $rep->states->pluck('id')->toArray();

            if (empty($allowedStateIds)) {
                $this->info("Skipping {$rep->name} (No Territory Assigned)");
                continue;
            }

            // B. Find leads ONLY in those states
            $leads = DentalOffice::whereIn('state_id', $allowedStateIds)
                ->whereNull('sales_rep_id') // Only unassigned leads
                ->take(5) // Limit 5 per rep
                ->get();

            foreach ($leads as $lead) {
                // C. Assign Lead to Rep
                $lead->sales_rep_id = $rep->id;
                $lead->save();

                // D. Create Task
                Task::create([
                    'user_id' => $rep->id,
                    'dental_office_id' => $lead->id,
                    'status' => 'pending',
                    'ai_suggested_approach' => $this->generateAiStrategy($lead),
                    'iteration_id' => $batchId,
                ]);
                $totalAssigned++;
            }

            if ($leads->count() > 0) {
                $this->info('Assigned ' . $leads->count() . " leads to {$rep->name}.");
            } else {
                $this->info("{$rep->name} is free, but has no new leads available in their territory.");
            }
        }

        $this->info("Batch Complete! Total Assigned: $totalAssigned");
        return 0;
    }

    private function generateAiStrategy($lead)
    {
        return 'AI Analysis Pending: Based on ' . ($lead->city ?? 'location') . ', pitch our standard growth package.';
    }
}
