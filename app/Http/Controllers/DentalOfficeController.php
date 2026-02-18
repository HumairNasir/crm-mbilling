<?php

namespace App\Http\Controllers;

use App\Models\DentalOffice;
use App\Models\State;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Imports\DentalOfficesImport;
use Maatwebsite\Excel\Facades\Excel;

class DentalOfficeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DentalOffice::with(['region', 'state', 'salesRep']);

        // 1. FILTER TABLE VIEW (Permissions)
        if ($user->hasRole('RegionalManager')) {
            $regionIds = $user->regions->pluck('id');
            $query->whereIn('region_id', $regionIds);
        } elseif ($user->hasRole('AreaManager')) {
            $stateIds = $user->states->pluck('id');
            $query->whereIn('state_id', $stateIds);
        } elseif ($user->hasRole('SalesRepresentative')) {
            $territoryIds = $user->states->pluck('id');
            $query->whereIn('state_id', $territoryIds);
        }

        // SEARCH logic
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dr_name', 'like', "%{$search}%")
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

        // Data for dropdowns
        $regions = Region::all();
        $groupedStates = $this->getGroupedStatesForUser($user);

        return view('dental_offices', compact('dentalOffices', 'groupedStates', 'regions'));
    }

    public function store(Request $request)
    {
        // DEBUG: Check what is coming from the form
        \Log::info('Incoming Form Data:', $request->all());

        if (Auth::user()->hasRole('SalesRepresentative')) {
            return redirect()->back()->with('error', 'Access Denied');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'exists:regions,id'],
            'state_id' => ['required', 'exists:states,id'],
            'email' => ['nullable', 'email', 'max:191'],
            'sales_rep' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:20'],
            'receptive' => ['nullable', 'in:HOT,WARM,COLD'],
        ]);

        if ($validator->fails()) {
            // DEBUG: Log validation errors if it fails
            \Log::error('Validation Failed:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $office = new DentalOffice();
            $office->name = $request->name;
            $office->dr_name = $request->dr_name;
            $office->email = $request->email;
            // $office->phone = $request->phone;
            $office->address = $request->address;
            $office->contact_person = $request->contact_person;
            $office->description = $request->description;
            $office->country = $request->country ?? 'United States';
            $office->region_id = $request->region_id;
            $office->state_id = $request->state_id;
            $office->sales_rep_id = $request->sales_rep;
            $office->territory_id = $request->territory;
            $office->receptive = $request->receptive ?? 'COLD';
            $office->purchase_product = $request->purchase_product ?? 'No';
            $office->purchase_subscriptions = $request->purchase_subscriptions ?? 'No';
            $office->contact_date = $request->contact_date;
            $office->follow_up_date = $request->follow_up_date;

            // 2. PHONE FORMATTING LOGIC
            if ($request->phone) {
                // Remove everything except numbers (strip spaces, dashes, +)
                $rawPhone = preg_replace('/[^0-9]/', '', $request->phone);

                // If it starts with '1' (country code), remove it for consistent formatting
                if (strlen($rawPhone) == 11 && substr($rawPhone, 0, 1) == '1') {
                    $rawPhone = substr($rawPhone, 1);
                }

                // If we have exactly 10 digits, format as +1 (XXX) XXX-XXXX
                if (strlen($rawPhone) == 10) {
                    $formattedPhone =
                        '+1 (' . substr($rawPhone, 0, 3) . ') ' . substr($rawPhone, 3, 3) . '-' . substr($rawPhone, 6);
                    $office->phone = $formattedPhone;
                } else {
                    // If length is weird (not 10), just save what the user typed to avoid data loss
                    $office->phone = $request->phone;
                }
            }

            $office->save();

            \Log::info('Office saved successfully with ID: ' . $office->id);
            return redirect()->back()->with('success', 'Office created successfully.');
        } catch (\Exception $e) {
            // DEBUG: Log any database errors
            \Log::error('Database Save Error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $office = DentalOffice::with(['region', 'state', 'salesRep'])->find($id);
        if (!$office) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $valid_reps = User::role('SalesRepresentative')
            ->whereHas('states', function ($q) use ($office) {
                $q->where('states.id', $office->state_id);
            })
            ->get(['id', 'name']);

        return response()->json(['office' => $office, 'valid_reps' => $valid_reps]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->hasRole('SalesRepresentative')) {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $office = DentalOffice::findOrFail($id);

        // Fill all fields from request
        $office->name = $request->name;
        $office->dr_name = $request->dr_name;
        $office->email = $request->email;
        $office->phone = $request->phone;
        $office->address = $request->address;
        $office->contact_person = $request->contact_person;
        $office->description = $request->description;
        $office->country = $request->country;
        $office->region_id = $request->region_id;
        $office->state_id = $request->state_id;
        $office->sales_rep_id = $request->sales_rep;
        $office->territory_id = $request->territory;
        $office->receptive = $request->receptive;
        $office->contacted_source = $request->contacted_source;
        $office->purchase_product = $request->purchase_product;
        $office->purchase_subscriptions = $request->purchase_subscriptions;
        $office->contact_date = $request->contact_date;
        $office->follow_up_date = $request->follow_up_date;

        $office->save();

        return response()->json(['success' => 'Updated successfully']);
    }

    // --- AJAX HELPER: Get States for a specific Region ---
    public function getStatesByRegion($region_id)
    {
        $states = State::where('region_id', $region_id)->get(['id', 'name']);
        return response()->json(['states' => $states]);
    }

    // --- AJAX HELPER: Get Sales Reps for a specific State ---
    public function getSalesReps($state_id)
    {
        $reps = User::role('SalesRepresentative')
            ->whereHas('states', function ($q) use ($state_id) {
                $q->where('states.id', $state_id);
            })
            ->get(['id', 'name']);

        return response()->json(['reps' => $reps]);
    }

    private function getGroupedStatesForUser($user)
    {
        $query = State::with('region');
        if ($user->hasRole('RegionalManager')) {
            $query->whereIn('region_id', $user->regions->pluck('id'));
        } elseif ($user->hasRole('AreaManager')) {
            $query->whereIn('id', $user->states->pluck('id'));
        }
        return $query->get()->groupBy('region.name');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        $import = new DentalOfficesImport();

        // Check extension and pass the string 'Csv' explicitly
        if ($extension === 'csv' || $extension === 'txt') {
            $import->import($file, null, 'Csv');
        } else {
            $import->import($file);
        }

        $skipped = [];
        if ($import->failures()->isNotEmpty()) {
            foreach ($import->failures() as $failure) {
                $skipped[] = [
                    'row' => $failure->row(),
                    'name' => $failure->values()['name'] ?? 'Unknown',
                    'reason' => $failure->errors()[0],
                ];
            }
        }

        return redirect()
            ->back()
            ->with([
                'success' => 'Import process completed.',
                'skipped_entries' => $skipped,
            ]);
    }
}
