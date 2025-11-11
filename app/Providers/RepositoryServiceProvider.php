<?php

namespace App\Providers;

use App\Repositories\IllegalObjectRepository;
use App\Repositories\Interfaces\IllegalObjectRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(IllegalObjectRepositoryInterface::class,IllegalObjectRepository::class);
    }
}
