<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClnMIcd10MdTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cln_m_icd10_md')->delete();
        
        \DB::table('cln_m_icd10_md')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name_ar' => 'هضمية',
                'name_en' => 'Gastroenterology',
                'act' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name_ar' => 'الطب الباطني',
                'name_en' => 'Internal Medicine',
                'act' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name_ar' => 'أطفال',
                'name_en' => 'Pediatric',
                'act' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name_ar' => 'أمراض معدية',
                'name_en' => 'Infectious Diseases',
                'act' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name_ar' => 'جراحة عامة',
                'name_en' => 'General Surgery',
                'act' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'name_ar' => 'أمراض الرئة',
                'name_en' => 'Pulmonology',
                'act' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'name_ar' => 'جراحة الصدر',
                'name_en' => 'Thoracic Surgery',
                'act' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'name_ar' => 'عصبية',
                'name_en' => 'Neurology',
                'act' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'name_ar' => 'جراحة عصبية',
                'name_en' => 'Neuro Surgery',
                'act' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'name_ar' => 'جلدية',
                'name_en' => 'Dermatology',
                'act' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'name_ar' => 'أذن أنف حنجرة',
                'name_en' => 'Ear, Nose, Throat',
                'act' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'name_ar' => 'جراحة عظمية',
                'name_en' => 'Orthopedic Surgery',
                'act' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'name_ar' => 'كلوي',
                'name_en' => 'Nephrology',
                'act' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'name_ar' => 'مسالك بولية',
                'name_en' => 'Urology',
                'act' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'name_ar' => 'عينية',
                'name_en' => 'Ophtalmology',
                'act' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'name_ar' => 'امراض الدم والاورام',
                'name_en' => 'Hematology & Oncology',
                'act' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'name_ar' => 'نسائية وتوليد',
                'name_en' => 'Obstetric Gynecology',
                'act' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'name_ar' => 'قلبية',
                'name_en' => 'Cardiology',
                'act' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'name_ar' => 'أسنان',
                'name_en' => 'Dentist',
                'act' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'name_ar' => 'جراحة القلب والأوعية الدموية',
                'name_en' => 'Cardiovascular Surgery',
                'act' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'name_ar' => 'غدد',
                'name_en' => 'Endocrinology',
                'act' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'name_ar' => 'جراحة الاوعية الدموية',
                'name_en' => 'Vascular Surgery',
                'act' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'name_ar' => 'نفسية',
                'name_en' => 'Psychology',
                'act' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'name_ar' => 'الروماتيزم',
                'name_en' => 'Rheumatology',
                'act' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'name_ar' => 'طبيب عام',
                'name_en' => 'General Practitioner',
                'act' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'name_ar' => 'جراحة أطفال',
                'name_en' => 'Pediatric Surgery',
                'act' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'name_ar' => 'تخدير',
                'name_en' => 'Anasthesia',
                'act' => 1,
            ),
        ));
        
        
    }
}