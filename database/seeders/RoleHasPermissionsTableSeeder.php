<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_has_permissions')->delete();
        
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'role_id' => 2,
            ),
            1 => 
            array (
                'permission_id' => 1,
                'role_id' => 3,
            ),
            2 => 
            array (
                'permission_id' => 1,
                'role_id' => 4,
            ),
            3 => 
            array (
                'permission_id' => 1,
                'role_id' => 5,
            ),
            4 => 
            array (
                'permission_id' => 2,
                'role_id' => 2,
            ),
            5 => 
            array (
                'permission_id' => 2,
                'role_id' => 3,
            ),
            6 => 
            array (
                'permission_id' => 2,
                'role_id' => 4,
            ),
            7 => 
            array (
                'permission_id' => 3,
                'role_id' => 2,
            ),
            8 => 
            array (
                'permission_id' => 3,
                'role_id' => 3,
            ),
            9 => 
            array (
                'permission_id' => 3,
                'role_id' => 4,
            ),
            10 => 
            array (
                'permission_id' => 4,
                'role_id' => 2,
            ),
            11 => 
            array (
                'permission_id' => 4,
                'role_id' => 3,
            ),
            12 => 
            array (
                'permission_id' => 4,
                'role_id' => 4,
            ),
            13 => 
            array (
                'permission_id' => 5,
                'role_id' => 2,
            ),
            14 => 
            array (
                'permission_id' => 5,
                'role_id' => 3,
            ),
            15 => 
            array (
                'permission_id' => 5,
                'role_id' => 4,
            ),
            16 => 
            array (
                'permission_id' => 6,
                'role_id' => 2,
            ),
            17 => 
            array (
                'permission_id' => 6,
                'role_id' => 3,
            ),
            18 => 
            array (
                'permission_id' => 6,
                'role_id' => 4,
            ),
            19 => 
            array (
                'permission_id' => 7,
                'role_id' => 2,
            ),
            20 => 
            array (
                'permission_id' => 7,
                'role_id' => 3,
            ),
            21 => 
            array (
                'permission_id' => 7,
                'role_id' => 4,
            ),
            22 => 
            array (
                'permission_id' => 8,
                'role_id' => 2,
            ),
            23 => 
            array (
                'permission_id' => 8,
                'role_id' => 3,
            ),
            24 => 
            array (
                'permission_id' => 8,
                'role_id' => 4,
            ),
            25 => 
            array (
                'permission_id' => 9,
                'role_id' => 2,
            ),
            26 => 
            array (
                'permission_id' => 9,
                'role_id' => 3,
            ),
            27 => 
            array (
                'permission_id' => 9,
                'role_id' => 4,
            ),
            28 => 
            array (
                'permission_id' => 10,
                'role_id' => 2,
            ),
            29 => 
            array (
                'permission_id' => 10,
                'role_id' => 4,
            ),
            30 => 
            array (
                'permission_id' => 11,
                'role_id' => 2,
            ),
            31 => 
            array (
                'permission_id' => 11,
                'role_id' => 4,
            ),
            32 => 
            array (
                'permission_id' => 12,
                'role_id' => 2,
            ),
            33 => 
            array (
                'permission_id' => 12,
                'role_id' => 4,
            ),
            34 => 
            array (
                'permission_id' => 13,
                'role_id' => 2,
            ),
            35 => 
            array (
                'permission_id' => 13,
                'role_id' => 3,
            ),
            36 => 
            array (
                'permission_id' => 13,
                'role_id' => 4,
            ),
            37 => 
            array (
                'permission_id' => 14,
                'role_id' => 2,
            ),
            38 => 
            array (
                'permission_id' => 14,
                'role_id' => 3,
            ),
            39 => 
            array (
                'permission_id' => 14,
                'role_id' => 4,
            ),
            40 => 
            array (
                'permission_id' => 15,
                'role_id' => 2,
            ),
            41 => 
            array (
                'permission_id' => 15,
                'role_id' => 3,
            ),
            42 => 
            array (
                'permission_id' => 15,
                'role_id' => 4,
            ),
            43 => 
            array (
                'permission_id' => 16,
                'role_id' => 2,
            ),
            44 => 
            array (
                'permission_id' => 16,
                'role_id' => 3,
            ),
            45 => 
            array (
                'permission_id' => 16,
                'role_id' => 4,
            ),
        ));
        
        
    }
}