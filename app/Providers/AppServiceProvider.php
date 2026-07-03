<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('face-status', function (Request $request) {
            $identity = $request->user()?->id
                ? 'u:' . $request->user()->id
                : 'ip:' . $request->ip();

            return [
                Limit::perMinute(360)->by($identity),
                Limit::perMinute(720)->by('ip:' . $request->ip()),
            ];
        });

        RateLimiter::for('face-action', function (Request $request) {
            $identity = $request->user()?->id
                ? 'u:' . $request->user()->id
                : 'ip:' . $request->ip();

            return [
                Limit::perMinute(30)->by($identity),
                Limit::perMinute(120)->by('ip:' . $request->ip()),
            ];
        });

        RateLimiter::for('face-frame', function (Request $request) {
            $identity = $request->user()?->id
                ? 'u:' . $request->user()->id
                : 'ip:' . $request->ip();

            return [
                Limit::perMinute(180)->by($identity),
                Limit::perMinute(360)->by('ip:' . $request->ip()),
            ];
        });
    }
}
