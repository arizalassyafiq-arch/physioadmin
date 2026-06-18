<?php

namespace App\Providers;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Observers\InterventionObserver;
use App\Observers\MedicalRecordObserver;
use App\Observers\PatientObserver;
use App\Policies\InterventionPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
    public function boot(): void
    {
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
        Gate::policy(Intervention::class, InterventionPolicy::class);

        Patient::observe(PatientObserver::class);
        MedicalRecord::observe(MedicalRecordObserver::class);
        Intervention::observe(InterventionObserver::class);

        RateLimiter::for('login', function (Request $request) {
            $key = Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });

        app()->setLocale('id');
        Carbon::setLocale('id');
        date_default_timezone_set(config('app.timezone'));
    }
}
