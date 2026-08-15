<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GnrMClinicsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('gnr_m_clinics')->delete();
        
        \DB::table('gnr_m_clinics')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name_ar' => 'العلاج الفيزيائي',
                'name_en' => 'Physical Treatment',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            1 => 
            array (
                'id' => 3,
                'name_ar' => 'عينية',
                'name_en' => 'Ophthalmology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            2 => 
            array (
                'id' => 4,
                'name_ar' => 'أذن انف حنجرة ',
                'name_en' => 'ENT',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            3 => 
            array (
                'id' => 5,
                'name_ar' => 'مخبر',
                'name_en' => 'LAB',
                'act' => 1,
                'type' => 2,
                'linked' => 0,
            ),
            4 => 
            array (
                'id' => 7,
                'name_ar' => 'صدرية',
                'name_en' => 'Respiration',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            5 => 
            array (
                'id' => 8,
                'name_ar' => 'توليد و نسائية',
                'name_en' => 'Gynaecology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            6 => 
            array (
                'id' => 9,
                'name_ar' => 'عصبية',
                'name_en' => 'Neurology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            7 => 
            array (
                'id' => 10,
                'name_ar' => 'عظمية',
                'name_en' => 'Orthopedics',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            8 => 
            array (
                'id' => 11,
                'name_ar' => 'أطفال',
                'name_en' => 'Pediatrics',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            9 => 
            array (
                'id' => 13,
                'name_ar' => 'تجميل ',
                'name_en' => 'Beauty ',
                'act' => 1,
                'type' => 5,
                'linked' => 0,
            ),
            10 => 
            array (
                'id' => 16,
                'name_ar' => 'غدد الصم والسكري',
                'name_en' => 'Endocrine',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            11 => 
            array (
                'id' => 17,
                'name_ar' => 'كلية ',
                'name_en' => 'Nephrology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            12 => 
            array (
                'id' => 18,
                'name_ar' => 'قلبية',
                'name_en' => 'Cardiology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            13 => 
            array (
                'id' => 19,
                'name_ar' => 'هضمية',
                'name_en' => 'Gastrology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            14 => 
            array (
                'id' => 21,
                'name_ar' => 'جلدية',
                'name_en' => 'Dermatology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            15 => 
            array (
                'id' => 23,
                'name_ar' => 'أشعة',
                'name_en' => 'X-Ray',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            16 => 
            array (
                'id' => 25,
                'name_ar' => 'أمراض دم وأورام',
                'name_en' => 'Oncology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            17 => 
            array (
                'id' => 28,
                'name_ar' => 'جراحة عامة',
                'name_en' => 'General Surgery',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            18 => 
            array (
                'id' => 41,
                'name_ar' => 'طوارىء وتمريض',
                'name_en' => 'Emergency',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            19 => 
            array (
                'id' => 42,
                'name_ar' => 'أسنان',
                'name_en' => 'Dentist',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            20 => 
            array (
                'id' => 44,
                'name_ar' => 'رعاية منزلية',
                'name_en' => 'Home Care',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            21 => 
            array (
                'id' => 45,
                'name_ar' => 'علاج نفسي',
                'name_en' => 'Psychiatry',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            22 => 
            array (
                'id' => 46,
                'name_ar' => 'طب أسرة وعام',
                'name_en' => 'Family & GP',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            23 => 
            array (
                'id' => 47,
                'name_ar' => 'طبقي محوري',
                'name_en' => 'CT-Scan',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            24 => 
            array (
                'id' => 48,
                'name_ar' => 'بانوراما',
                'name_en' => 'Panorama',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            25 => 
            array (
                'id' => 49,
                'name_ar' => 'إيكو',
                'name_en' => 'Eko',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            26 => 
            array (
                'id' => 50,
                'name_ar' => 'سيفالوميتريك',
                'name_en' => 'Cephalometric',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            27 => 
            array (
                'id' => 51,
                'name_ar' => 'اختبار الجهد',
                'name_en' => 'Stress Test',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            28 => 
            array (
                'id' => 52,
                'name_ar' => 'كثافة عظمية',
                'name_en' => 'Dexa',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            29 => 
            array (
                'id' => 53,
                'name_ar' => 'مامو غرافي',
                'name_en' => 'Mamography',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            30 => 
            array (
                'id' => 54,
                'name_ar' => 'إيكو رباعي',
                'name_en' => 'ECHO 4D',
                'act' => 0,
                'type' => 3,
                'linked' => 0,
            ),
            31 => 
            array (
                'id' => 55,
                'name_ar' => 'وظائف الرئة',
                'name_en' => 'Lung Test',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            32 => 
            array (
                'id' => 56,
                'name_ar' => 'تغذية',
                'name_en' => 'Nutrition',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            33 => 
            array (
                'id' => 57,
                'name_ar' => 'العلاج بالموسيقى',
                'name_en' => 'Music Therapy',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            34 => 
            array (
                'id' => 58,
                'name_ar' => 'الليزر ',
                'name_en' => 'Laser',
                'act' => 1,
                'type' => 6,
                'linked' => 0,
            ),
            35 => 
            array (
                'id' => 61,
                'name_ar' => 'جراحة أوعية',
                'name_en' => 'Vascular Surgery',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            36 => 
            array (
                'id' => 62,
                'name_ar' => 'عمليات',
                'name_en' => 'Minor Surgeries',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            37 => 
            array (
                'id' => 63,
                'name_ar' => 'جراحة أطفال',
                'name_en' => 'Children`s Surgery',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            38 => 
            array (
                'id' => 64,
            'name_ar' => 'جلدية (2)',
                'name_en' => 'Dermatology',
                'act' => 1,
                'type' => 1,
                'linked' => 21,
            ),
            39 => 
            array (
                'id' => 65,
                'name_ar' => 'رنين مغناطيسي ',
                'name_en' => 'Magnetic resonance',
                'act' => 0,
                'type' => 3,
                'linked' => 0,
            ),
            40 => 
            array (
                'id' => 66,
                'name_ar' => 'أشعة ظليلة',
                'name_en' => 'X-Ray',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            41 => 
            array (
                'id' => 67,
                'name_ar' => 'مفاصل',
                'name_en' => 'Orthopedics',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            42 => 
            array (
                'id' => 68,
                'name_ar' => 'تنظير هضمي',
                'name_en' => 'Endoscopy',
                'act' => 1,
                'type' => 7,
                'linked' => 0,
            ),
            43 => 
            array (
                'id' => 69,
                'name_ar' => 'جراحة بولية ',
                'name_en' => 'Urology',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            44 => 
            array (
                'id' => 70,
                'name_ar' => 'قياس الوزن ',
                'name_en' => 'in body',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            45 => 
            array (
                'id' => 71,
                'name_ar' => 'عصبية 2',
                'name_en' => 'Neurology',
                'act' => 1,
                'type' => 1,
                'linked' => 9,
            ),
            46 => 
            array (
                'id' => 72,
                'name_ar' => 'سكري وبدانة',
                'name_en' => 'Diabetes & Obesity',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            47 => 
            array (
                'id' => 73,
                'name_ar' => 'جلدية 3',
                'name_en' => 'Dermatology',
                'act' => 1,
                'type' => 1,
                'linked' => 21,
            ),
            48 => 
            array (
                'id' => 74,
                'name_ar' => 'أطفال 2',
                'name_en' => 'Pediatric',
                'act' => 1,
                'type' => 1,
                'linked' => 11,
            ),
            49 => 
            array (
                'id' => 75,
                'name_ar' => 'أسنان أطفال',
                'name_en' => 'Pediatrics dentis',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            50 => 
            array (
                'id' => 76,
                'name_ar' => 'أسنان تجميل',
                'name_en' => 'Cosmetic Dentistry',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            51 => 
            array (
                'id' => 77,
                'name_ar' => 'أسنان تعويضات',
                'name_en' => 'Dental Prosthesis',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            52 => 
            array (
                'id' => 78,
                'name_ar' => 'أسنان تقويم',
                'name_en' => 'Orthodontics',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            53 => 
            array (
                'id' => 79,
                'name_ar' => 'أسنان جراحة',
                'name_en' => 'Oral And Maxillofacial Surgery',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            54 => 
            array (
                'id' => 80,
                'name_ar' => 'أسنان لبية',
                'name_en' => 'Pulp Therapy',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            55 => 
            array (
                'id' => 81,
                'name_ar' => 'أسنان لثة',
                'name_en' => 'Gum And Periodontal Diseases',
                'act' => 1,
                'type' => 4,
                'linked' => 0,
            ),
            56 => 
            array (
                'id' => 82,
                'name_ar' => 'عظمية 2 ',
                'name_en' => 'Orthopedics',
                'act' => 1,
                'type' => 1,
                'linked' => 10,
            ),
            57 => 
            array (
                'id' => 83,
                'name_ar' => 'الأسرة السعيدة ',
                'name_en' => 'Happy Family',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            58 => 
            array (
                'id' => 85,
                'name_ar' => 'أسنان تجميل 2',
                'name_en' => 'اسنان تجميل 2',
                'act' => 1,
                'type' => 4,
                'linked' => 76,
            ),
            59 => 
            array (
                'id' => 86,
                'name_ar' => 'إرشاد وظيفي',
                'name_en' => 'ارشاد وظيفي',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            60 => 
            array (
                'id' => 87,
                'name_ar' => 'بديكور ومنيكور',
                'name_en' => 'بديكور ومنيكور',
                'act' => 1,
                'type' => 5,
                'linked' => 13,
            ),
            61 => 
            array (
                'id' => 88,
                'name_ar' => 'هضمية 2',
                'name_en' => 'Gastrology',
                'act' => 1,
                'type' => 1,
                'linked' => 19,
            ),
            62 => 
            array (
                'id' => 89,
                'name_ar' => 'أذنية',
                'name_en' => 'ENT',
                'act' => 0,
                'type' => 1,
                'linked' => 4,
            ),
            63 => 
            array (
                'id' => 91,
                'name_ar' => 'عيادة تمريض',
                'name_en' => 'Nursing',
                'act' => 1,
                'type' => 1,
                'linked' => 41,
            ),
            64 => 
            array (
                'id' => 92,
                'name_ar' => 'سماعات اذنية ',
                'name_en' => 'سماعات اذنية ',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            65 => 
            array (
                'id' => 93,
                'name_ar' => 'انتانات',
                'name_en' => 'انتانات',
                'act' => 0,
                'type' => 1,
                'linked' => 0,
            ),
            66 => 
            array (
                'id' => 94,
                'name_ar' => 'تمريض قلبية',
                'name_en' => 'Nursing',
                'act' => 1,
                'type' => 1,
                'linked' => 18,
            ),
            67 => 
            array (
                'id' => 95,
                'name_ar' => 'طباعة صور الاشعة',
                'name_en' => 'X-Ray',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            68 => 
            array (
                'id' => 98,
                'name_ar' => 'زيارة خارجية',
                'name_en' => 'Nursing',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            69 => 
            array (
                'id' => 99,
                'name_ar' => 'تجميل 2 قديم',
                'name_en' => 'تجميل 2 قديم',
                'act' => 0,
                'type' => 5,
                'linked' => 0,
            ),
            70 => 
            array (
                'id' => 100,
                'name_ar' => 'جراحة الثدي',
                'name_en' => 'Breast surgery',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            71 => 
            array (
                'id' => 101,
                'name_ar' => 'الطب العام',
                'name_en' => 'GP',
                'act' => 1,
                'type' => 1,
                'linked' => 41,
            ),
            72 => 
            array (
                'id' => 102,
                'name_ar' => 'تغذية2',
                'name_en' => 'Nutrition',
                'act' => 1,
                'type' => 1,
                'linked' => 56,
            ),
            73 => 
            array (
                'id' => 103,
                'name_ar' => 'عصبية اطفال',
                'name_en' => 'عصبية اطفال',
                'act' => 1,
                'type' => 1,
                'linked' => 11,
            ),
            74 => 
            array (
                'id' => 104,
                'name_ar' => 'إسعاف ليلي',
                'name_en' => 'Emergency',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            75 => 
            array (
                'id' => 105,
                'name_ar' => 'اشعة تداخلية',
                'name_en' => 'اشعة تداخلية',
                'act' => 1,
                'type' => 3,
                'linked' => 0,
            ),
            76 => 
            array (
                'id' => 106,
                'name_ar' => 'غدد اطفال',
                'name_en' => 'Pediatric Endocrinology',
                'act' => 1,
                'type' => 1,
                'linked' => 11,
            ),
            77 => 
            array (
                'id' => 108,
                'name_ar' => 'تجميل2',
                'name_en' => 'تجميل2',
                'act' => 1,
                'type' => 5,
                'linked' => 13,
            ),
            78 => 
            array (
                'id' => 109,
                'name_ar' => 'برمجة لغوية عصبية',
                'name_en' => 'NLP',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            79 => 
            array (
                'id' => 110,
                'name_ar' => 'ليزر ديكا',
                'name_en' => 'ليزر ديكا',
                'act' => 1,
                'type' => 5,
                'linked' => 0,
            ),
            80 => 
            array (
                'id' => 111,
                'name_ar' => 'تقرير طبي',
                'name_en' => 'تقرير طبي',
                'act' => 0,
                'type' => 1,
                'linked' => 16,
            ),
            81 => 
            array (
                'id' => 112,
                'name_ar' => 'هضمية  اطفال',
                'name_en' => 'هضمية  اطفال',
                'act' => 1,
                'type' => 1,
                'linked' => 0,
            ),
            82 => 
            array (
                'id' => 113,
            'name_ar' => 'عيادة اطفال (3)',
            'name_en' => 'عيادة اطفال (3)',
                'act' => 1,
                'type' => 1,
                'linked' => 11,
            ),
            83 => 
            array (
                'id' => 114,
                'name_ar' => 'نفسية 1',
                'name_en' => 'نفسية 1',
                'act' => 1,
                'type' => 1,
                'linked' => 45,
            ),
            84 => 
            array (
                'id' => 115,
                'name_ar' => 'معاينة',
                'name_en' => 'معاينة',
                'act' => 1,
                'type' => 1,
                'linked' => 45,
            ),
        ));
        
        
    }
}