<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(AdminStaffSeeder::class);
        $this->call(GnrMCitiesTableSeeder::class);
        $this->call(GnrMAreasTableSeeder::class);
        $this->call(GnrMNationalityTableSeeder::class);
        $this->call(GnrMClinicsTableSeeder::class);
        $this->call(ClnMAddonsTableSeeder::class);
        $this->call(ClnMIcd10CatTableSeeder::class);
        $this->call(ClnMIcd10MdTableSeeder::class);
        $this->call(ClnMIcd10TableSeeder::class);
        $this->call(ClnMMedicalHisCatsTableSeeder::class);
        $this->call(ClnMMedicalHisTableSeeder::class);
        $this->call(ClnMServicesTableSeeder::class);
        $this->call(AdsTableSeeder::class);
        $this->call(ClinicDoctorsSeeder::class);
        $this->call(DoctorSimulationSeeder::class);
    }
}
