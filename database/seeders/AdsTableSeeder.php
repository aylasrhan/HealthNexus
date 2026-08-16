<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('ads')->delete();
        
        \DB::table('ads')->insert(array (
            0 => 
            array (
                'id' => 1,
                'img' => 'ads/ppJDrGoC1CICkaNKjGzYarM5yv0qskepV7LWNorS.png',
                'text' => 'تفقتبفتغتلات',
                'statue' => 0,
                'created_at' => '2026-08-15 09:03:51',
                'updated_at' => '2026-08-15 09:03:51',
            ),
        ));
        
        
    }
}