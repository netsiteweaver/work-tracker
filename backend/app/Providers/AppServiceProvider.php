<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share dark mode preference with all views
        View::composer('*', function ($view) {
            $darkMode = false;
            if (auth()->check()) {
                $darkMode = Setting::get('dark_mode', false, auth()->id());
            }
            $view->with('darkMode', $darkMode);
        });
    }
}
