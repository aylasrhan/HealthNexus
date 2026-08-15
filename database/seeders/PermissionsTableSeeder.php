<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'المستخدمين',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'قائمة المستخدمين',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'صلاحيات المستخدمين',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'الاعدادات',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'الاقسام',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'اضافة مستخدم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'تعديل مستخدم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'حذف مستخدم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'عرض صلاحية',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:27',
                'updated_at' => '2023-04-24 17:10:27',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'اضافة صلاحية',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'تعديل صلاحية',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'حذف صلاحية',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'اضافة قسم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'تعديل قسم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'حذف قسم',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'الاشعارات',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 17:10:28',
                'updated_at' => '2023-04-24 17:10:28',
            ),
        ));
        
        
    }
}