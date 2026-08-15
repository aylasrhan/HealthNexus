<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClnMAddonsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cln_m_addons')->delete();
        
        \DB::table('cln_m_addons')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'vh3d871mb1',
                'icon' => 'dignos',
                'name_ar' => 'التشخيص التفريقي',
                'name_en' => 'Diagnosis',
                'addon' => 'med_proc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 6,
                'short_code' => 'dia',
                'act' => 1,
                'color' => '#94c953',
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'jm4nndrak0',
                'icon' => 'PC',
                'name_ar' => 'الشكاية الرئيسية',
                'name_en' => 'Patient\'s complaint',
                'addon' => 'med_proc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 4,
                'short_code' => 'com',
                'act' => 1,
                'color' => '#d1e140',
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'kjrepzcpai',
                'icon' => 'exam',
                'name_ar' => 'الفحص السريري',
                'name_en' => 'Clinical examination',
                'addon' => 'med_proc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 7,
                'short_code' => 'cln',
                'act' => 1,
                'color' => '#a629b5',
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'cykhelccih',
                'icon' => 'story',
                'name_ar' => 'القصة المرضية',
                'name_en' => 'Pathological story',
                'addon' => 'med_proc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 8,
                'short_code' => 'str',
                'act' => 1,
                'color' => '#693bb7',
            ),
            4 => 
            array (
                'id' => 5,
                'code' => '5s1knov62b',
                'icon' => 'ICD',
                'name_ar' => 'ICD10',
                'name_en' => 'ICD10',
                'addon' => 'icd10_icpc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 5,
                'short_code' => 'icd',
                'act' => 1,
                'color' => '#f34c39',
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'iexje9eo9s',
                'icon' => 'ICPC2',
                'name_ar' => 'ICPC',
                'name_en' => 'ICPC',
                'addon' => 'icd10_icpc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 3,
                'short_code' => 'icp',
                'act' => 1,
                'color' => '#dd1e6b',
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'ww0i5f8nzz',
                'icon' => 'vs',
                'name_ar' => 'العلامات الحيوية',
                'name_en' => 'Vital signs',
                'addon' => 'vital_signs',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 2,
                'short_code' => 'vit',
                'act' => 1,
                'color' => '#4559bc',
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'ba1r68m7c3',
                'icon' => 'growth',
                'name_ar' => 'مؤشرات النمو',
                'name_en' => 'Growth indicators',
                'addon' => 'growth_indicators',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 10,
                'short_code' => 'grw',
                'act' => 1,
                'color' => '#6ac1f7',
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'dd1q42qqvk',
                'icon' => 'mr',
                'name_ar' => 'السوابق المرضية',
                'name_en' => 'Medical history',
                'addon' => 'medical_history',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 1,
                'req' => 1,
                'ord' => 1,
                'short_code' => 'his',
                'act' => 1,
                'color' => '#56b65c',
            ),
            9 => 
            array (
                'id' => 10,
                'code' => '3o1myb940',
                'icon' => 'ecg',
                'name_ar' => 'تخطيط قلب',
                'name_en' => 'ECG',
                'addon' => 'ecg',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 11,
                'short_code' => 'ecg',
                'act' => 0,
                'color' => '#009e8f',
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'gk3dlj39le',
                'icon' => 'eko',
                'name_ar' => ' إيكو القلب',
                'name_en' => 'Eko heart',
                'addon' => 'eko_heart',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 12,
                'short_code' => 'eko',
                'act' => 0,
                'color' => '#835f51',
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'v99ov38jw6',
                'icon' => 'notes',
                'name_ar' => 'الملاحظات',
                'name_en' => 'Notes',
                'addon' => 'med_proc',
                'clinic' => 0,
                'service' => 0,
                'req_load' => 0,
                'req' => 0,
                'ord' => 9,
                'short_code' => 'not',
                'act' => 1,
                'color' => '#bc8f8f',
            ),
        ));
        
        
    }
}