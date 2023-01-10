<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /* Variation Templates */
        $this->app->bind(
            'App\\Interfaces\\VariationTemplateInterface',
            'App\\Repositories\\VariationTemplateRepository'
        );
        
        /* Categories */
        $this->app->bind(
            'App\\Interfaces\\CategoryInterface',
            'App\\Repositories\\CategoryRepository'
        );
        
        /* Products */
        $this->app->bind(
            'App\\Interfaces\\ProductInterface',
            'App\\Repositories\\ProductRepository'
        );
        
        /* Unit */
        $this->app->bind(
            'App\\Interfaces\\UnitInterface',
            'App\\Repositories\\UnitRepository'
        );
        
        /* Business Location */
        $this->app->bind(
            'App\\Interfaces\\BusinessLocationInterface',
            'App\\Repositories\\BusinessLocationRepository'
        );
        
        /* Invoice Layouts */
        $this->app->bind(
            'App\\Interfaces\\InvoiceLayoutInterface',
            'App\\Repositories\\InvoiceLayoutRepository'
        );
        
        /* Invoice Scheme */
        $this->app->bind(
            'App\\Interfaces\\InvoiceSchemeInterface',
            'App\\Repositories\\InvoiceSchemeRepository'
        );
        
        /* Sell price group */
        $this->app->bind(
            'App\\Interfaces\\SellPriceGroupInterface',
            'App\\Repositories\\SellPriceGroupRepository'
        );
        
        /* Tax Rate */
        $this->app->bind(
            'App\Interfaces\\TaxRateInterface',
            'App\Repositories\\TaxRateInterface'
        );
        
        /* Brand */
        $this->app->bind(
            'App\\Interfaces\\BrandInterface',
            'App\Repositories\\BrandRepository'
        );
        
        /* Warranty */
        $this->app->bind(
            'App\Interfaces\WarrantyInterface',
            'App\Repositories\WarrantyRepository'
        );

        /* Business */
        $this->app->bind(
            'App\Interfaces\BusinessInterface',
            'App\Repositories\BusinessRepository'
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
