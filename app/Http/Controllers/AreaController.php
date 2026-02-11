<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Redirect;

class AreaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'AreaManager');
        })->with(['states', 'regionalManager']); // Eager load states and boss

        if ($user->roles[0]->name == 'CountryManager') {
            $query->where('country_manager_id', $user->id);
        } elseif ($user->roles[0]->name == 'RegionalManager') {
            $query->where('regional_manager_id', $user->id);
        }

        $area_managers = $query->get();
        $region_list = Region::all();

        // Get Regional Managers for Dropdown
        $regional_managers = User::whereHas('roles', function ($q) {
            $q->where('name', 'RegionalManager');
        })->get();

        return view('area-manager', compact('area_managers', 'region_list', 'regional_managers'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'required|min:8',
            'assign' => 'required|numeric', // Regional Manager ID
            'areas' => 'required|array', // States
            'areas.*' => 'exists:states,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);

        // HIERARCHY
        $user->regional_manager_id = $request->assign; // The selected Regional Manager

        // Auto-assign Country Manager from the Regional Manager
        $regionalManager = User::find($request->assign);
        if ($regionalManager) {
            $user->country_manager_id = $regionalManager->country_manager_id;
        }

        $user->save();

        // ROLE
        DB::insert('insert into model_has_roles (role_id, model_type, model_id) values (?, ?, ?)', [
            4,
            'App\Models\User',
            $user->id,
        ]);

        // SYNC STATES
        if ($request->has('areas')) {
            $user->states()->sync($request->areas);
        }

        return response()->json(['success' => 'Area Manager created successfully.']);
    }

    public function edit($id)
    {
        $user = User::with(['states', 'regionalManager.regions'])->find($id);

        if ($user) {
            return response()->json([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'regional_manager_id' => $user->regional_manager_id,
                'area_ids' => $user->states->pluck('id'), // Currently assigned states
                // Send back the regions owned by the parent Regional Manager (for display)
                'parent_regions' => $user->regionalManager ? $user->regionalManager->regions : [],
            ]);
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'assign' => 'required|numeric',
                'areas' => 'nullable|array',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->regional_manager_id = $request->assign;

            if ($request->has('password') && !empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // SYNC STATES
            // Note: If areas is empty (user deselected all), sync([]) clears them.
            $user->states()->sync($request->input('areas', []));

            return response()->json(['message' => 'User updated successfully']);
        }

        return response()->json(['message' => 'User not found'], 404);
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

    // --- NEW LOGIC: Fetch States Grouped by Regions for a Specific Manager ---

    public function getStatesByManager(Request $request)
    {
        $managerId = $request->manager_id;
        $userId = $request->user_id;

        if (!$managerId) {
            return response()->json(['regions' => [], 'states' => []]);
        }

        try {
            // 1. Get Regional Manager and their Regions
            $manager = User::with('regions')->find($managerId);
            if (!$manager) {
                return response()->json(['regions' => [], 'states' => []]);
            }

            $regionIds = $manager->regions->pluck('id')->toArray();

            // 2. CHECK "TAKEN" STATES (The Fix)
            // Only consider a state "Taken" if it is assigned to another AREA MANAGER (Role 4).
            // We Ignore Sales Reps (Role 5) so they don't block the Area Manager.
            $query = DB::table('state_user')
                ->join('model_has_roles', 'state_user.user_id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', 4); // <--- STRICTLY CHECK ROLE 4

            if ($userId) {
                $query->where('state_user.user_id', '!=', $userId);
            }

            $takenStateIds = $query->pluck('state_user.state_id')->toArray();

            // 3. Fetch States
            $states = State::whereIn('region_id', $regionIds)->whereNotIn('id', $takenStateIds)->get();

            // 4. Group by Region
            $groupedStates = [];
            foreach ($states as $state) {
                $parentRegion = $manager->regions->where('id', $state->region_id)->first();
                $regionName = $parentRegion ? $parentRegion->name : 'Other Regions';
                $groupedStates[$regionName][] = $state;
            }

            return response()->json([
                'manager_regions' => $manager->regions,
                'grouped_states' => $groupedStates,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Get States Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
