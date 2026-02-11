<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DentalOffice;
use App\Models\Region;
use App\Models\State;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Client::with(['dentalOffice.state', 'dentalOffice.region', 'salesRep']);

        // 1. HIERARCHY FILTER FOR CLIENT LIST
        if ($user->hasRole('RegionalManager')) {
            $regionIds = $user->regions->pluck('id');
            $query->whereHas('dentalOffice', function ($q) use ($regionIds) {
                $q->whereIn('region_id', $regionIds);
            });
        } elseif ($user->hasRole('AreaManager')) {
            $stateIds = $user->states->pluck('id');
            $query->whereHas('dentalOffice', function ($q) use ($stateIds) {
                $q->whereIn('state_id', $stateIds);
            });
        } elseif ($user->hasRole('SalesRepresentative')) {
            $query->where('sales_rep_id', $user->id);
        }

        // SEARCH: office name, doctor name, area, sales rep
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhereHas('salesRep', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dentalOffice.state', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $clients = $query
            ->latest()
            ->paginate(10)
            ->appends(request()->query());

        // 2. PREPARE DROPDOWN DATA (Fixes Undefined Variable Errors)
        $regions = Region::all(); // Default for CM
        $areas = collect(); // Default empty
        $dental_offices = collect(); // Default empty

        if ($user->hasRole('RegionalManager')) {
            // RM only sees their Regions
            $regions = $user->regions;
            // RM sees Areas inside their Regions
            $areas = State::whereIn('region_id', $regions->pluck('id'))->get();
            // RM sees Offices inside their Regions
            $dental_offices = DentalOffice::whereIn('region_id', $regions->pluck('id'))->get();
        } elseif ($user->hasRole('AreaManager')) {
            // AM sees their assigned States (Areas)
            $areas = $user->states;
            // AM sees their relevant Regions
            $regions = Region::whereIn('id', $areas->pluck('region_id')->unique())->get();
            // AM sees Offices inside their States
            $dental_offices = DentalOffice::whereIn('state_id', $areas->pluck('id'))->get();
        } elseif ($user->hasRole('SalesRepresentative')) {
            // Rep sees only their assigned offices
            $dental_offices = DentalOffice::where('sales_rep_id', $user->id)->get();
        }

        // 3. PASS ALL VARIABLES TO VIEW
        return view('client', compact('clients', 'regions', 'areas', 'dental_offices'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'dental_office_id' => 'required|exists:dental_offices,id',
            'subscription_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 1. Create Client
        $client = new Client();
        $client->name = $request->name;
        $client->contact_person = $request->contact_person;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->dental_office_id = $request->dental_office_id;
        $client->sales_rep_id = Auth::user()->id;
        $client->status = 'Active';
        $client->subscription_amount = $request->subscription_amount;
        $client->save();

        // 2. UNIVERSAL TASK CONVERSION LOGIC
        // Update ANY task (Pending or Completed) linked to this Dental Office to 'converted'.
        // We removed 'where(user_id)' so it works even if a Manager converts a Rep's task.

        Task::where('dental_office_id', $request->dental_office_id)
            ->whereIn('status', ['pending', 'completed']) // Target both types
            ->update([
                'status' => 'converted',
                'completed_at' => Carbon::now(),
            ]);

        // 3. Update Dental Office Status
        $office = DentalOffice::find($request->dental_office_id);
        if ($office) {
            $office->receptive = 'Warm';
            $office->save();
        }

        // 4. Send notification to all users
        $officeName = $office ? $office->name : 'Unknown Office';
        NotificationService::leadConverted($client->name, $officeName, Auth::id());

        return redirect()->back()->with('success', 'Client converted successfully!');
    }

    public function edit($id)
    {
        $client = Client::with(['dentalOffice.state', 'dentalOffice.region'])->find($id);
        if (!$client) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Pre-fill Dropdowns for Edit Modal based on current client location
        $currentRegionId = $client->dentalOffice->region_id;
        $currentStateId = $client->dentalOffice->state_id;

        $valid_areas = State::where('region_id', $currentRegionId)->get();
        $valid_offices = DentalOffice::where('state_id', $currentStateId)->get();

        return response()->json([
            'client' => $client,
            'region_id' => $currentRegionId,
            'state_id' => $currentStateId,
            'valid_areas' => $valid_areas,
            'valid_offices' => $valid_offices,
        ]);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;

        if ($request->filled('status')) {
            $client->status = $request->status;
        }

        if ($request->filled('dental_office_id')) {
            $client->dental_office_id = $request->dental_office_id;
        }

        // --- UPDATE SUBSCRIPTION ---
        if ($request->has('subscription_amount')) {
            $client->subscription_amount = $request->subscription_amount;
        }
        $client->save();

        return response()->json(['success' => 'Client updated successfully']);
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if ($client) {
            $client->delete();
            return redirect()->back()->with('success', 'Client deleted successfully');
        }
        return redirect()->back()->with('error', 'Client not found');
    }

    // --- AJAX Helper: Get Offices for a State ---
    public function getOfficesByArea($state_id)
    {
        $offices = DentalOffice::where('state_id', $state_id)->get(['id', 'name']);
        return response()->json($offices);
    }
}
