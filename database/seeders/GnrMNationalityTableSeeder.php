<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GnrMNationalityTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('gnr_m_nationality')->delete();
        
        \DB::table('gnr_m_nationality')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name_ar' => 'سوري',
                'name_en' => 'Syrian',
            ),
            1 => 
            array (
                'id' => 2,
                'name_ar' => 'فلسطيني',
                'name_en' => 'Palestinian',
            ),
            2 => 
            array (
                'id' => 3,
                'name_ar' => 'مغترب',
                'name_en' => 'مغترب',
            ),
        ));
        
        
    }
}