<?php

namespace Quintavalentina\Modules;

use Quintavalentina\Modules\Modules;
use Quintavalentina\Modules\Contracts\Repository;
use Quintavalentina\Modules\Providers\ConsoleServiceProvider;
use Quintavalentina\Modules\Providers\GeneratorServiceProvider;
use Quintavalentina\Modules\Providers\MigrationServiceProvider;
use Quintavalentina\Modules\Providers\RepositoryServiceProvider;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * @var bool Indicates if loading of the provider is deferred.
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/modules.php' => config_path('modules.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/stubs/' => config('modules.custom_stubs'),
        ], 'stubs');

        $this->app['modules']->register();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modules.php', 'modules'
        );

        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(GeneratorServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);

        $this->app->singleton('modules', function ($app) {
            $repository = $app->make(Repository::class);

            return new Modules($app, $repository);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string
     */
    public function provides()
    {
        return ['modules'];
    }

    public static function compiles()
    {
        $repository = app()->make('modules');
        $modules    = $repository->all();
        $files      = [];

        foreach ($modules as $slug => $properties) {
            $namespace       = $repository->resolveNamespace($properties);
            $file            = $repository->getPath()."/{$namespace}/Providers/{$namespace}ServiceProvider.php";
            $serviceProvider = $repository->getNamespace().'\\'.$namespace."\\Providers\\{$namespace}ServiceProvider";

            if (class_exists($serviceProvider)) {
                $files = array_merge($files, forward_static_call([$serviceProvider, 'compiles']));
            }
        }

        return array_map('realpath', $files);
    }
}
