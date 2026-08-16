<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GnrMCitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('gnr_m_cities')->delete();
        
        \DB::table('gnr_m_cities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => ' دمشق',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => ' ريف دمشق',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'درعا',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'السويداء',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'القنيطرة',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'حلب',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'الرقة',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'إدلب',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'حمص',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'حماة',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'اللاذقية',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'طرطوس',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'دير الزور',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'الحسكة',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'الاردن',
            ),
            15 => 
            array (
                'id' => 20,
                'name' => 'الامارات',
            ),
            16 => 
            array (
                'id' => 21,
                'name' => 'لبنان',
            ),
            17 => 
            array (
                'id' => 23,
                'name' => 'مصر',
            ),
            18 => 
            array (
                'id' => 26,
                'name' => 'بغداد',
            ),
            19 => 
            array (
                'id' => 31,
                'name' => 'العراق',
            ),
            20 => 
            array (
                'id' => 39,
                'name' => 'القامشلي',
            ),
            21 => 
            array (
                'id' => 40,
                'name' => 'المانيا',
            ),
            22 => 
            array (
                'id' => 46,
                'name' => 'الجزائر',
            ),
            23 => 
            array (
                'id' => 47,
                'name' => 'مغرب',
            ),
            24 => 
            array (
                'id' => 50,
                'name' => 'مصياف',
            ),
            25 => 
            array (
                'id' => 60,
                'name' => 'بحرين',
            ),
            26 => 
            array (
                'id' => 63,
                'name' => 'البرازيل',
            ),
            27 => 
            array (
                'id' => 64,
                'name' => 'بوكمال',
            ),
            28 => 
            array (
                'id' => 132,
                'name' => 'قدسيا',
            ),
            29 => 
            array (
                'id' => 133,
                'name' => 'خان الشيح',
            ),
            30 => 
            array (
                'id' => 82,
                'name' => 'سلمية',
            ),
            31 => 
            array (
                'id' => 90,
                'name' => 'دنمارك',
            ),
            32 => 
            array (
                'id' => 135,
                'name' => 'جبل الرز',
            ),
            33 => 
            array (
                'id' => 98,
                'name' => 'جبلة',
            ),
            34 => 
            array (
                'id' => 100,
                'name' => 'بانياس',
            ),
            35 => 
            array (
                'id' => 101,
                'name' => 'خارج البلد',
            ),
            36 => 
            array (
                'id' => 102,
                'name' => 'تركيا',
            ),
            37 => 
            array (
                'id' => 103,
                'name' => 'بريطانيا',
            ),
            38 => 
            array (
                'id' => 109,
                'name' => 'بيروت',
            ),
            39 => 
            array (
                'id' => 110,
                'name' => 'ايطاليا',
            ),
            40 => 
            array (
                'id' => 113,
                'name' => 'جبل الشيخ',
            ),
            41 => 
            array (
                'id' => 117,
                'name' => 'عين العرب',
            ),
            42 => 
            array (
                'id' => 118,
                'name' => 'سويد',
            ),
            43 => 
            array (
                'id' => 122,
                'name' => 'هولندا',
            ),
            44 => 
            array (
                'id' => 124,
                'name' => 'سعودية',
            ),
            45 => 
            array (
                'id' => 126,
                'name' => 'كندا',
            ),
            46 => 
            array (
                'id' => 134,
                'name' => 'دبي',
            ),
            47 => 
            array (
                'id' => 136,
                'name' => 'صافيتا',
            ),
            48 => 
            array (
                'id' => 137,
                'name' => 'قطنا',
            ),
            49 => 
            array (
                'id' => 138,
                'name' => 'قرى الاسد',
            ),
            50 => 
            array (
                'id' => 139,
                'name' => 'مساكن الحرس',
            ),
            51 => 
            array (
                'id' => 140,
                'name' => 'قرداحة',
            ),
            52 => 
            array (
                'id' => 141,
                'name' => 'نجها',
            ),
            53 => 
            array (
                'id' => 142,
                'name' => 'القدم',
            ),
            54 => 
            array (
                'id' => 143,
                'name' => 'حي الورود',
            ),
            55 => 
            array (
                'id' => 144,
                'name' => 'دمر البلد',
            ),
            56 => 
            array (
                'id' => 145,
                'name' => 'زبداني',
            ),
            57 => 
            array (
                'id' => 146,
                'name' => 'جيزة',
            ),
            58 => 
            array (
                'id' => 147,
                'name' => 'برامكة',
            ),
            59 => 
            array (
                'id' => 148,
                'name' => 'مشروع دمر',
            ),
            60 => 
            array (
                'id' => 149,
                'name' => 'أمريكا',
            ),
            61 => 
            array (
                'id' => 150,
                'name' => 'الكسوة',
            ),
            62 => 
            array (
                'id' => 151,
                'name' => 'جديدة',
            ),
            63 => 
            array (
                'id' => 152,
                'name' => 'مزة',
            ),
            64 => 
            array (
                'id' => 153,
                'name' => 'ميدان',
            ),
            65 => 
            array (
                'id' => 154,
                'name' => 'مغترب',
            ),
            66 => 
            array (
                'id' => 155,
                'name' => 'السيدة زينب',
            ),
            67 => 
            array (
                'id' => 156,
                'name' => 'عين منين',
            ),
            68 => 
            array (
                'id' => 157,
                'name' => 'جمرايا',
            ),
            69 => 
            array (
                'id' => 158,
                'name' => 'رياض',
            ),
            70 => 
            array (
                'id' => 159,
                'name' => 'مالكي',
            ),
            71 => 
            array (
                'id' => 160,
                'name' => 'النبك',
            ),
            72 => 
            array (
                'id' => 161,
                'name' => 'الصبورة',
            ),
            73 => 
            array (
                'id' => 162,
                'name' => 'عمان',
            ),
            74 => 
            array (
                'id' => 163,
                'name' => 'وادي المشاريع',
            ),
            75 => 
            array (
                'id' => 164,
                'name' => 'برزة',
            ),
            76 => 
            array (
                'id' => 165,
                'name' => 'الست زينب',
            ),
        ));
        
        
    }
}