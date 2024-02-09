<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use App\Models\State;
use App\Models\Territory;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Redirect;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $regional_managers = User::whereNotNull('country_manager_id')
        ->whereNull('regional_manager_id')
        ->whereNull('state_manager_id')
        ->get();

        $area_managers = User::whereNotNull('country_manager_id')
        ->whereNotNull('regional_manager_id')
        ->whereNull('state_manager_id')
        ->get();
        
        $region_list = Region::all();

         



        if ($regional_managers->isEmpty()) {
            // Handle case when no regional managers are found
            return redirect()->back()->with('error', 'No regional managers found.');
        }

        return view('area-manager', compact('regional_managers','region_list','area_managers'));
 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
       // Validate the request data
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'region' => 'required|numeric', 
            'password' => 'required',
            'area' => 'required', 
            'assign' => 'required',
        ];

      
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
           
            $errorsArray = [];

            foreach ($validator->errors()->toArray() as $field => $errors) {
                // Create an array for each field containing the error messages
                $errorsArray[$field] = array_map(function($error) {
                    return ucfirst($error);
                }, $errors);
            }
            
            // Return the validation errors
            return response()->json(['errors' => $errorsArray]);
            
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
         $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->country_manager_id = Auth::user()->id;
        $user->region_id = $request->input('region');

        $user->regional_manager_id = $request->input('assign');
        $user->state_id = $request->input('area');
        $user->password = Hash::make($request->input('password'));
        
        // Save the user
        $user->save();
        $user_id = $user->id; // Retrieve the ID of the saved user


        DB::insert('insert into model_has_roles (role_id, model_type, model_id) values (?, ?, ?)', [4, 'App\Models\User', $user_id]);

        return redirect()->route('regional_manager.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = User::find($id);

        if(!empty($user)){
            $userData   = array();
            $user_id                    = !empty($user->id) ? $user->id : '';
            $user_name                  = !empty($user->name) ? $user->name : '';
            $user_phone                 = !empty($user->phone) ? $user->phone : '';
            $user_email                 = !empty($user->email) ? $user->email : '';
            $user_address               = !empty($user->address) ? $user->address : '';
            $user_region_id             = !empty($user->region_id) ? $user->region_id : '';
            $user_country_manager_id    = !empty($user->country_manager_id) ? $user->country_manager_id : '';
            $user_regional_manager_id   = !empty($user->regional_manager_id) ? $user->regional_manager_id : '';
            $user_state_id  = !empty($user->state_id) ? $user->state_id : '';

            $area_list  =array();
            if($user_region_id){
                $area_list = State::where('region_id', $user_region_id)->get();
            }
            $userData = [
                'user_id'       => $user_id,
                'name'          => $user_name,
                'phone'         => $user_phone,
                'email'         => $user_email,
                'address'       => $user_address,
                'region_id'     => $user_region_id,
                'country_manager_id'    => $user_country_manager_id,
                'regional_manager_id'   => $user_regional_manager_id,
                'area_list'   => $area_list,
                'area_id'   => $user_state_id,
            ];
            
            // Return the array as a JSON response
            return response()->json($userData);

        } else {
            return response()->json(['message' => 'Something Went Wrong'], 404);
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

        if($user){
            // Validate the request data
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'region' => 'required|numeric', 
                'area' => 'required', 
                'assign' => 'required',
            ];

      
            $validator = Validator::make($request->all(), $rules);

            // Check if validation fails
            if ($validator->fails()) {
            
                $errorsArray = [];

                foreach ($validator->errors()->toArray() as $field => $errors) {
                    // Create an array for each field containing the error messages
                    $errorsArray[$field] = array_map(function($error) {
                        return ucfirst($error);
                    }, $errors);
                }
                
                // Return the validation errors
                return response()->json(['errors' => $errorsArray]);
                

            }

          
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->country_manager_id = Auth::user()->id;
            $user->region_id = $request->input('region');

            $user->regional_manager_id = $request->input('assign');
            $user->state_id = $request->input('area');
         
            if ($request->has('password') && !empty($request->input('password'))) {
                // Hash the password
                $hashedPassword = Hash::make($request->input('password'));
                $user->password = $hashedPassword;
            }

            
            // Save the user
            $user->save();

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
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Check if the associated record exists in model_has_roles table
        $recordExists = DB::table('model_has_roles')->where('model_id', $id)->exists();
    
        if ($recordExists) {
            // Delete the associated record from model_has_roles table
            DB::table('model_has_roles')->where('model_id', $id)->delete();
        }
    
        $user->delete();
    
        return Redirect::back();
    }

    public function getAreas($region_id)
    {
         
        $areas = State::where('region_id', $region_id)->get();
        return response()->json($areas);
    }

    
    public function getterritories($area_id)
    {
         
        $territories = Territory::where('state_id', $area_id)->get();
        return response()->json($territories);
    }
}
