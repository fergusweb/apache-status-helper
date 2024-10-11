<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\FetchApacheStatus;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the command here
        $this->commands(
            [
            FetchApacheStatus::class,
            ]
        );
    }
}
