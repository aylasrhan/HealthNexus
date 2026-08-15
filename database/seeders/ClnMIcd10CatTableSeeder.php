<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClnMIcd10CatTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cln_m_icd10_cat')->delete();
        
        \DB::table('cln_m_icd10_cat')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name_ar' => 'أمراض انتانية وطفيلية محددة',
                'name_en' => 'Certain infecious and parasitic diseases',
                'act' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name_ar' => 'أمراض تنشؤية',
                'name_en' => 'Neoplasms',
                'act' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name_ar' => 'أمراض الدم والأعضاء المنتجة للدم واضطرابات محددة تصيب الآلية المناعية',
                'name_en' => 'Diseases of blood and blood forming organs and certain disorders involving the immune mechanism',
                'act' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name_ar' => 'الأمراض الغدية الصماوية,التغذوية , والاستقلابية',
                'name_en' => 'Endocrine,nutritional and metabolic diseases',
                'act' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name_ar' => 'الاضطرابات العقلية والسلوكية',
                'name_en' => 'Mental and behavioural disorders',
                'act' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'name_ar' => 'أمراض الجهاز العصبي',
                'name_en' => 'Diseases of the nervous system',
                'act' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'name_ar' => 'أمراض العين وملحقاتها',
                'name_en' => 'Diseases of the eye and adnexa',
                'act' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'name_ar' => 'أمراض الأذن والناتئ الخشائي',
                'name_en' => 'Diseases of the ear and mastoid process',
                'act' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'name_ar' => 'أمراض الجهاز الدوراني',
                'name_en' => 'Diseases of the circulatory system',
                'act' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'name_ar' => 'أمراض الجهاز التنفسي',
                'name_en' => 'Diseases of the respiratory system',
                'act' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'name_ar' => 'أمراض الجهاز الهضمي',
                'name_en' => 'Diseases of the digestive system',
                'act' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'name_ar' => 'أمراض الجلد والنسيج تحت الجلد',
                'name_en' => 'Diseases of the skin and subcutanous tissue',
                'act' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'name_ar' => 'أمراض الجهاز العضلي الهيكلي والنسيج الضام',
                'name_en' => 'Diseases of the musculoskeletal system and connective tissue',
                'act' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'name_ar' => 'أمراض الجهاز البولي التناسلي',
                'name_en' => 'Diseases of the genitourinary system ',
                'act' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'name_ar' => 'الحمل,الولادة والنفاس',
                'name_en' => 'Pregnancy,childbirth and the puerperium',
                'act' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'name_ar' => 'حالات نوعية تحصل في الفترة حول الولادة',
                'name_en' => 'Certain conditions originating in the perinatal period',
                'act' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'name_ar' => 'تشوهات خلقية, أسواء تشكل وشذوذات صبغية',
                'name_en' => 'Congenital malformations,deformations and chromosomal abnormalities',
                'act' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'name_ar' => 'أعراض ,علامات وموجودات سريرية ومخبرية غير طبيعية ,غير مصنفة في مكان آخر',
                'name_en' => 'Symptoms,signs and abnormal clinical and laboratory findings not eleswhere clssified',
                'act' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'name_ar' => 'أذية,تسمم وعقابيل أخرى محددة من أسباب خارجية',
                'name_en' => 'Injury, poisoning and certain other consequences of external causes',
                'act' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'name_ar' => 'رموز لأغراض خاصة',
                'name_en' => 'Codes for special purposes',
                'act' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'name_ar' => 'أسباب خارجية للمراضة والوفاة',
                'name_en' => 'External causes of morbidity and mortality',
                'act' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'name_ar' => 'عوامل مؤثرة في الوضع الصحي والاتصال بالخدمات الصحية',
                'name_en' => 'Factors influencing health status and contact with health services',
                'act' => 1,
            ),
        ));
        
        
    }
}