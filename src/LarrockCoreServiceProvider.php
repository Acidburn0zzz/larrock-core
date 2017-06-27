<?php

namespace Larrock\Core;

use Illuminate\Support\ServiceProvider;
use Larrock\Core\Middleware\AdminMenu;
use Larrock\Core\Middleware\GetSeo;
use Larrock\Core\Middleware\VerifyLevel;

class LarrockCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'larrock');

        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/larrock'),
            __DIR__.'/views' => base_path('resources/views/larrock'),
            __DIR__.'/config/larrock-core-adminmenu.php' => config_path('larrock-core-adminmenu.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app['router']->aliasMiddleware('level', VerifyLevel::class);
        $this->app['router']->aliasMiddleware('LarrockAdminMenu', AdminMenu::class);
        $this->app['router']->aliasMiddleware('GetSeo', GetSeo::class);
        $this->app->make(AdminController::class);

        $this->mergeConfigFrom( __DIR__.'/config/larrock-core-adminmenu.php', 'larrock-core-adminmenu');

        if ( !class_exists('CreateLarrockConfigTable')){
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/0000_00_00_000000_create_config_table.php' => database_path('migrations/'.$timestamp.'_create_config_table.php')
            ], 'migrations');
        }
        if ( !class_exists('CreateLarrockSeoTable')){
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/0000_00_00_000000_create_seo_table.php' => database_path('migrations/'.$timestamp.'_create_seo_table.php')
            ], 'migrations');
        }
    }
}
