<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Super Admin',
                'role' => 'SuperAdmin',
                'email' => 'superadmin@kriss.com',
            ],
            [
                'name' => 'Country Manager',
                'role' => 'CountryManager',
                'email' => 'countrymanager@kriss.com',
            ],
            // [
            //     'name' => 'Regional Manager',
            //     'role' => 'RegionalManager',
            //     'email' => 'RegionalManager@kriss.com',
            // ],
            // [
            //     'name' => 'Area Manager',
            //     'role' => 'AreaManager',
            //     'email' => 'AreaManager@kriss.com',
            // ],
            // [
            //     'name' => 'Sales Representative',
            //     'role' => 'SalesRepresentative',
            //     'email' => 'SalesRepresentative@kriss.com',
            // ],
        ];
        // foreach ($users as $user) {
        //     $u = User::create([
        //         'name' => $user['name'],
        //         'email' => $user['email'],
        //         'password' => bcrypt('1234321%$#@!'),
        //     ]);
        //     if (isset($user['role'])) {
        //         $role = Role::findByName($user['role']);
        //         $u->assignRole($role);
        //     }
        // }
        foreach ($users as $user) {
            $u = User::updateOrCreate(
                ['email' => $user['email']], // Condition (check existence)
                [
                    'name' => $user['name'], // Used only if creating
                    'password' => Hash::make('1234321%$#@!'), // Always updates password
                ],
            );

            if (isset($user['role'])) {
                $u->syncRoles([$user['role']]);
            }
        }
    }
}
