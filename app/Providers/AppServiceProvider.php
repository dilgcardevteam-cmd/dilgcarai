<?php

namespace App\Providers;

use App\Models\Notebook;
use App\Models\Source;
use App\Policies\NotebookPolicy;
use App\Policies\SourcePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Notebook::class, NotebookPolicy::class);
        Gate::policy(Source::class, SourcePolicy::class);

        RateLimiter::for('ai-chat', function (Request $request): Limit {
            $maxAttempts = $request->user()?->isAdmin() ? 120 : 45;

            return Limit::perMinute($maxAttempts)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('uploads', function (Request $request): Limit {
            $maxAttempts = $request->user()?->isAdmin() ? 60 : 20;

            return Limit::perMinute($maxAttempts)->by($request->user()?->id ?: $request->ip());
        });
    }
}
