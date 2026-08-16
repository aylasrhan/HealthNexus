<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClnMMedicalHisCatsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cln_m_medical_his_cats')->delete();
        
        \DB::table('cln_m_medical_his_cats')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name_ar' => 'الحساسيات',
                'name_en' => 'الحساسيات',
                's_date' => 0,
                'e_date' => 0,
                'num' => 0,
                'active' => 0,
                'alert' => 1,
                'note' => 1,
                'year' => 1,
                'ord' => 1,
                'act' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name_ar' => 'السوابق الجراحية',
                'name_en' => 'العمليات السابقة',
                's_date' => 0,
                'e_date' => 0,
                'num' => 1,
                'active' => 0,
                'alert' => 0,
                'note' => 1,
                'year' => 1,
                'ord' => 2,
                'act' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name_ar' => 'الأمراض المزمنة',
                'name_en' => 'الأمراض المزمنة',
                's_date' => 0,
                'e_date' => 0,
                'num' => 0,
                'active' => 0,
                'alert' => 1,
                'note' => 1,
                'year' => 1,
                'ord' => 3,
                'act' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name_ar' => 'الادوية الدائمة',
                'name_en' => 'الادوية الدائمة',
                's_date' => 0,
                'e_date' => 0,
                'num' => 0,
                'active' => 0,
                'alert' => 1,
                'note' => 1,
                'year' => 1,
                'ord' => 4,
                'act' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name_ar' => 'العادات',
                'name_en' => 'العادات',
                's_date' => 0,
                'e_date' => 0,
                'num' => 0,
                'active' => 1,
                'alert' => 1,
                'note' => 1,
                'year' => 1,
                'ord' => 5,
                'act' => 1,
            ),
        ));
        
        
    }
}