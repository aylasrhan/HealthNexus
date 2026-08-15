<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'super_admin',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:17:41',
                'updated_at' => '2023-04-24 17:17:41',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'secretary',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:23:01',
                'updated_at' => '2023-04-24 17:23:01',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'doctor',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 19:13:52',
                'updated_at' => '2023-04-24 19:13:52',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'patient',
                'guard_name' => 'web',
                'created_at' => '2023-05-30 22:32:45',
                'updated_at' => '2023-05-30 22:32:45',
            ),
        ));
        
        
    }
}