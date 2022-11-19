<?php

namespace ITB\LaraPackage\Providers;

use Illuminate\Support\ServiceProvider;

class ProductionServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        \ITB\LaraPackage\Console\Commands\ProductionMake::class
    ];
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands($this->commands);
    }
}