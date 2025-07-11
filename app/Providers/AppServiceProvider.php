<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use App\Services\DependencyTest\BaseServiceInterface;
use App\Services\DependencyTest\ConcreteBaseService;
use App\Services\DependencyTest\TestServiceInterface;
use App\Services\DependencyTest\ConcreteTestService;
use App\Services\DependencyTest\ApplicationInstanceService;
use App\Services\DependencyTest\ApplicationInjectedService;
use App\Services\DependencyTest\RequestScopedService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to concrete implementations for dependency injection testing
        $this->app->singleton(BaseServiceInterface::class, ConcreteBaseService::class);
        $this->app->singleton(TestServiceInterface::class, ConcreteTestService::class);
        
        // Register ApplicationInstanceService as singleton to track application lifecycle
        $this->app->singleton(ApplicationInstanceService::class);
        
        // Register ApplicationInjectedService as singleton with Application injection
        $this->app->singleton(ApplicationInjectedService::class, function (Application $app) {
            return new ApplicationInjectedService($app);
        });
        
        // 🛡️ OCTANE-SAFE: Use scoped() for request-level singletons
        // This creates a new instance per request, even in Octane
        $this->app->scoped(RequestScopedService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
