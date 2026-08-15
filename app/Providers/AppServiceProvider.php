<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\back\Question;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

public function boot()
{
    Paginator::useBootstrapFour();
    if (DB::connection()->getDriverName() === 'mysql') {
        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
    }

    View::composer('layouts.main-sidebar', function ($view) {
        $user = auth()->user();
        $unansweredQuestionsCount = 0;

        if ($user?->hasSystemRole('doctor')) {
            $section = (int) $user->doctor?->subgrp;
            if ($section > 0) {
                $unansweredQuestionsCount = Question::query()
                    ->where('section', $section)
                    ->where(fn ($query) => $query->whereNull('answer')->orWhere('answer', ''))
                    ->count();
            }
        }

        $view->with('unansweredQuestionsCount', $unansweredQuestionsCount);
    });
}
}
