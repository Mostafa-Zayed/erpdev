<?php

namespace Modules\Garage\Providers;

use App\Utils\ModuleUtil;
use Illuminate\Database\Eloquent\Factory;

use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class GarageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        //TODO:Remove sidebar
        view::composer(['garage::layouts.partials.sidebar',
            'garage::layouts.partials.invoice_layout_settings',
            'garage::layouts.partials.pos_header',
            'garage::layouts.partials.header'
            ], function ($view) {
                if (auth()->user()->can('superadmin')) {
                    $__is_garage_enabled = true;
                } else {
                    $business_id = session()->get('user.business_id');
                    $module_util = new ModuleUtil();
                    $__is_garage_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'garage_module');
                }

                $view->with(compact('__is_garage_enabled'));
            });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('garage.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php',
            'garage'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/garage');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/garage';
        }, \Config::get('view.paths')), [$sourcePath]), 'garage');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/garage');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'garage');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'garage');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
