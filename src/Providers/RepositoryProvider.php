<?php
namespace Andersonef\Repositories\Providers;

use \Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{

    protected $commands = [
        \Andersonef\Repositories\Console\Commands\RepositoryCommand::class,
    ];
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
