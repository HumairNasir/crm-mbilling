<?php

namespace App\Http\Controllers;

use App\Models\DentalOffice;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DentalOfficeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DentalOffice::with(['region', 'state', 'salesRep']);

        // 1. FILTER TABLE VIEW (Who sees what?)
        if ($user->hasRole('RegionalManager')) {
            $regionIds = $user->regions->pluck('id');
            $query->whereIn('region_id', $regionIds);
        } elseif ($user->hasRole('AreaManager')) {
            $stateIds = $user->states->pluck('id');
            $query->whereIn('state_id', $stateIds);
        } elseif ($user->hasRole('SalesRepresentative')) {
            // New: All offices in my assigned Territory (States)
            $territoryIds = $user->states->pluck('id');
            $query->whereIn('state_id', $territoryIds);
        }
        // CountryManager sees all (no filter)

        // SEARCH: office name, region, area(state)
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('region', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('state', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $dentalOffices = $query
            ->latest()
            ->paginate(10)
            ->appends(request()->query());

        // 2. PREPARE DROPDOWN DATA (Grouped States)
        // Format: ['Region Name' => [State1, State2], ...]
        $groupedStates = $this->getGroupedStatesForUser($user);

        return view('dental_offices', compact('dentalOffices', 'groupedStates'));
    }

    public function store(Request $request)
    {
        // 1. BLOCK SALES REPS
        if (Auth::user()->hasRole('SalesRepresentative')) {
            return redirect()->back()->with('error', 'Access Denied');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id', // This is the "Location" dropdown
            'sales_rep' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. AUTO-DETECT REGION from the selected State
        $state = State::find($request->state_id);
        $region_id = $state->region_id;

        $office = new DentalOffice();
        $office->name = $request->name;
        $office->email = $request->email;
        $office->phone = $request->phone;
        $office->contact_person = $request->contact_person;
        $office->address = $request->address;
        $office->country = $request->country ?? 'United States';
        $office->dr_name = $request->dr_name;

        // Hierarchy
        $office->state_id = $request->state_id;
        $office->region_id = $region_id; // Inferred
        $office->sales_rep_id = $request->sales_rep;
        $office->territory_id = $request->territory;

        $office->receptive = 'Cold'; // Default
        $office->purchase_product = 'No'; // Default

        $office->save();

        return redirect()->back()->with('success', 'Dental Office added successfully.');
    }

    public function edit($id)
    {
        $office = DentalOffice::with(['region', 'state', 'salesRep'])->find($id);

        if (!$office) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $valid_reps = [];
        try {
            if ($office->state_id) {
                $valid_reps = User::role('SalesRepresentative')
                    ->whereHas('states', function ($q) use ($office) {
                        // FIX: Change 'id' to 'states.id' to avoid ambiguity
                        $q->where('states.id', $office->state_id);
                    })
                    ->get(['id', 'name']);
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching reps: ' . $e->getMessage());
        }

        return response()->json([
            'office' => $office,
            'valid_reps' => $valid_reps,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->hasRole('SalesRepresentative')) {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $office = DentalOffice::find($id);
        if (!$office) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $office->name = $request->name;
        $office->email = $request->email;
        $office->phone = $request->phone;
        $office->contact_person = $request->contact_person;
        $office->address = $request->address;
        $office->country = $request->country;
        $office->dr_name = $request->dr_name;

        // If location changed
        if ($request->filled('state_id')) {
            $state = State::find($request->state_id);
            $office->state_id = $request->state_id;
            $office->region_id = $state->region_id; // Auto-update region
        }

        if ($request->filled('sales_rep')) {
            $office->sales_rep_id = $request->sales_rep;
        }
        if ($request->filled('territory')) {
            $office->territory_id = $request->territory;
        }

        $office->save();

        return response()->json(['success' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        if (Auth::user()->hasRole('SalesRepresentative')) {
            return redirect()->back()->with('error', 'Access Denied');
        }
        DentalOffice::destroy($id);
        return redirect()->back()->with('success', 'Deleted successfully');
    }

    // --- HELPER: Get Sales Reps for a specific State ---
    public function getSalesReps($state_id)
    {
        $reps = User::role('SalesRepresentative')
            ->whereHas('states', function ($q) use ($state_id) {
                $q->where('id', $state_id);
            })
            ->get(['id', 'name']);

        return response()->json(['reps' => $reps]);
    }

    // --- PRIVATE HELPER: Group States by Region Logic ---
    private function getGroupedStatesForUser($user)
    {
        $query = State::with('region');

        if ($user->hasRole('RegionalManager')) {
            // Only states in my regions
            $regionIds = $user->regions->pluck('id');
            $query->whereIn('region_id', $regionIds);
        } elseif ($user->hasRole('AreaManager')) {
            // Only states assigned to me
            $stateIds = $user->states->pluck('id');
            $query->whereIn('id', $stateIds);
        } elseif ($user->hasRole('SalesRepresentative')) {
            return collect(); // Empty collection (Hidden anyway)
        }
        // CountryManager gets everything (no filter)

        // Fetch and Group
        $states = $query->get();
        return $states->groupBy('region.name');
    }
}
