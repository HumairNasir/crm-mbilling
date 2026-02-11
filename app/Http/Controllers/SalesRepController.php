<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Redirect;

class SalesRepController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = User::role('SalesRepresentative')->with(['states', 'regionalManager', 'stateManager']);

        // Task Counters
        $query->withCount([
            'tasks as pending_tasks_count' => function ($q) {
                $q->where('status', 'pending');
            },
            'tasks as assigned_today_count' => function ($q) {
                $q->whereDate('created_at', Carbon::today());
            },
            'tasks as total_completed_count' => function ($q) {
                $q->whereIn('status', ['completed', 'converted']);
            },
        ]);

        if ($user->hasRole('CountryManager')) {
            $query->where('country_manager_id', $user->id);
        } elseif ($user->hasRole('RegionalManager')) {
            $query->where('regional_manager_id', $user->id);
        } elseif ($user->hasRole('AreaManager')) {
            $query->where('state_manager_id', $user->id);
        }

        // SEARCH: name, state
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhereHas('states', function ($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%");
                });
            });
        }

        $sales_reps = $query->paginate(10)->appends(request()->query());

        // Global Stats Calculation (Same as before)
        $teamQuery = User::role('SalesRepresentative');
        if ($user->hasRole('CountryManager')) {
            $teamQuery->where('country_manager_id', $user->id);
        } elseif ($user->hasRole('RegionalManager')) {
            $teamQuery->where('regional_manager_id', $user->id);
        } elseif ($user->hasRole('AreaManager')) {
            $teamQuery->where('state_manager_id', $user->id);
        }

        $teamIds = $teamQuery->pluck('id');

        $global_pending = \App\Models\Task::whereIn('user_id', $teamIds)->where('status', 'pending')->count();
        $global_assigned_today = \App\Models\Task::whereIn('user_id', $teamIds)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $global_total_completed = \App\Models\Task::whereIn('user_id', $teamIds)
            ->whereIn('status', ['completed', 'converted'])
            ->count();

        $activeIteration = \App\Models\Iteration::where('status', 'active')->first();
        $batchId = $activeIteration ? $activeIteration->id : '-';

        $region_list = Region::all();
        $regional_managers = User::role('RegionalManager')->get();
        $area_managers_list = [];
        if ($user->hasRole('RegionalManager')) {
            $area_managers_list = User::role('AreaManager')->where('regional_manager_id', $user->id)->get();
        }

        return view(
            'sales-rep',
            compact(
                'sales_reps',
                'region_list',
                'regional_managers',
                'area_managers_list',
                'global_pending',
                'global_assigned_today',
                'global_total_completed',
                'activeIteration',
                'batchId',
            ),
        );
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'areas' => 'required|array',
            'areas.*' => 'exists:states,id',
        ];

        if ($currentUser->hasRole('CountryManager')) {
            $rules['regional_manager'] = 'required'; // Only need RM, Region is inferred
            $rules['area_manager'] = 'required';
        } elseif ($currentUser->hasRole('RegionalManager')) {
            $rules['area_manager'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->address = $request->address;

        // Hierarchy Logic
        if ($currentUser->hasRole('CountryManager')) {
            $user->country_manager_id = $currentUser->id;
            $user->regional_manager_id = $request->regional_manager;
            $user->state_manager_id = $request->area_manager;

            // Infer Region from Regional Manager (First one as primary, or null if strictly many-to-many)
            $rm = User::find($request->regional_manager);
            // $user->region_id = $rm->region_id; // Optional if using single column
        } elseif ($currentUser->hasRole('RegionalManager')) {
            $user->country_manager_id = $currentUser->country_manager_id;
            $user->regional_manager_id = $currentUser->id;
            $user->state_manager_id = $request->area_manager;
        } elseif ($currentUser->hasRole('AreaManager')) {
            $user->country_manager_id = $currentUser->country_manager_id;
            $user->regional_manager_id = $currentUser->regional_manager_id;
            $user->state_manager_id = $currentUser->id;
        }

        $user->save();

        DB::insert('insert into model_has_roles (role_id, model_type, model_id) values (?, ?, ?)', [
            5,
            'App\Models\User',
            $user->id,
        ]);

        if ($request->has('areas')) {
            $user->states()->sync($request->areas);
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Sales Representative added successfully.']);
        }
        return redirect()->route('sales_rep.index')->with('success', 'Sales Representative added successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'areas' => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->has('password') && !empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        // Allow Hierarchical updates if Country Manager
        if (Auth::user()->hasRole('CountryManager')) {
            if ($request->has('regional_manager')) {
                $user->regional_manager_id = $request->regional_manager;
            }
            if ($request->has('area_manager')) {
                $user->state_manager_id = $request->area_manager;
            }
        }

        $user->save();
        $user->states()->sync($request->areas ?? []);

        return response()->json(['message' => 'Sales Rep updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->states()->detach();
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully.');
        }
        return redirect()->back()->with('error', 'User not found.');
    }

    public function edit($id)
    {
        $user = User::with(['states', 'regionalManager', 'stateManager'])->find($id);

        if ($user) {
            return response()->json([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'regional_manager_id' => $user->regional_manager_id,
                'state_manager_id' => $user->state_manager_id,
                'area_ids' => $user->states->pluck('id'),
            ]);
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    // --- AJAX Helpers ---

    // 1. Get Regional Manager's Regions (For display badge)
    public function getRegionsByManager($id)
    {
        $manager = User::with('regions')->find($id);
        if (!$manager) {
            return response()->json([]);
        }
        return response()->json($manager->regions);
    }

    // 2. Get Area Managers under a Regional Manager
    public function getAreaManagers($regional_manager_id)
    {
        $managers = User::role('AreaManager')->where('regional_manager_id', $regional_manager_id)->get();
        return response()->json($managers);
    }

    // 3. NEW: Get States Grouped by Region (For Dropdown)
    public function getManagerStates($area_manager_id, $sales_rep_id = null)
    {
        try {
            $manager = User::with(['states', 'regionalManager.regions'])->find($area_manager_id);
            if (!$manager) {
                return response()->json(['grouped_states' => []]);
            }

            // Get states taken by OTHER Sales Reps
            $takenStateIds = DB::table('state_user')
                ->join('model_has_roles', 'state_user.user_id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', 5) // Sales Rep Role
                ->where('state_user.user_id', '!=', $sales_rep_id) // Exclude current user if editing
                ->pluck('state_user.state_id')
                ->toArray();

            // Filter available states
            $availableStates = $manager->states->filter(function ($state) use ($takenStateIds) {
                return !in_array($state->id, $takenStateIds);
            });

            // Group them by Region Name
            // We need to fetch the Region info for each state manually or via relation
            $groupedStates = [];

            // Get parent Regional Manager's regions for naming
            $parentRegions = $manager->regionalManager ? $manager->regionalManager->regions : collect([]);

            foreach ($availableStates as $state) {
                // Find matching region
                $region = $parentRegions->where('id', $state->region_id)->first();
                $regionName = $region ? $region->name : 'Other';

                $groupedStates[$regionName][] = $state;
            }

            return response()->json(['grouped_states' => $groupedStates]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
