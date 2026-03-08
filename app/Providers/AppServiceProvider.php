<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
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
        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            if ($event->command === 'serve') {
                $this->app->make(\Illuminate\Contracts\Console\Kernel::class)
                    ->call('migrate', ['--seed' => true, '--force' => true]);
            }
        });
    }
}
