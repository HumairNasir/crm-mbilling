<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Redirect;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 1. Get Regional Managers
        $regional_managers = User::whereNotNull('country_manager_id')
            ->whereNull('regional_manager_id')
            ->whereNull('state_manager_id')
            ->with('regions') // Eager load for the table badge loop
            ->get();

        // 2. LOGIC: Get only regions that are NOT assigned to anyone
        // Get all region_ids currently in the pivot table
        $takenRegionIds = DB::table('region_user')->pluck('region_id')->toArray();

        // Filter: Show only regions NOT in the taken list
        $available_regions = Region::whereNotIn('id', $takenRegionIds)->get();

        // Keep full list for fallback/display if needed, but 'available' is main for Add
        $region_list = Region::all();

        return view('regional_manager', compact('regional_managers', 'region_list', 'available_regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Validate
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'required|min:8',
            'region' => 'required|array', // CHANGE: 'numeric' -> 'array'
            'region.*' => 'exists:regions,id', // Validate each item exists
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // 2. Create User
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->password = Hash::make($request->input('password'));

        // 3. Assign Hierarchy
        $user->country_manager_id = Auth::user()->id;
        // REMOVED: $user->region_id = ... (We don't use this column anymore)

        $user->save();

        // 4. Attach Multiple Regions
        if ($request->has('region')) {
            $user->regions()->sync($request->input('region'));
        }

        // 5. Assign Role
        DB::insert('insert into model_has_roles (role_id, model_type, model_id) values (?, ?, ?)', [
            3,
            'App\Models\User',
            $user->id,
        ]);

        return response()->json(['success' => 'Regional Manager created successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('regions')->find($id);

        if ($user) {
            // --- EDIT LOGIC: Show Free Regions + This User's Regions ---

            // 1. Get IDs taken by OTHER people (exclude current user)
            $takenByOthers = DB::table('region_user')->where('user_id', '!=', $id)->pluck('region_id')->toArray();

            // 2. Get regions that are NOT taken by others
            $valid_regions = Region::whereNotIn('id', $takenByOthers)->get();

            return response()->json([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'region_ids' => $user->regions->pluck('id'),
                'valid_regions' => $valid_regions, // <--- Sending the specific list for this user
            ]);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'region' => 'required|array', // CHANGE: numeric -> array
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            // REMOVED: $user->region_id update

            if ($request->has('password') && !empty($request->input('password'))) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            // Sync new regions (this handles adding new ones and removing unchecked ones)
            $user->regions()->sync($request->input('region'));

            return response()->json(['message' => 'User updated successfully']);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            // Remove Role
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully.');
        }

        return redirect()->back()->with('error', 'User not found.');
    }
}
