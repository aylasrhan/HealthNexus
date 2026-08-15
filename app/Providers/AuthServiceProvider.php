<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\back\Appointment;
use App\Policies\AppointmentPolicy;
use App\Models\back\gnr_m_patients;
use App\Policies\PatientPolicy;
use App\Models\back\cln_x_visits;
use App\Policies\VisitPolicy;
use App\Models\back\Question;
use App\Policies\QuestionPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        gnr_m_patients::class => PatientPolicy::class,
        cln_x_visits::class => VisitPolicy::class,
        Question::class => QuestionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
