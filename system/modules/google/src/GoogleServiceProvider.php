<?php

namespace Google;

use App\Providers\ModuleServiceProviderBase;
use App\Support\JustInTwig;

class GoogleServiceProvider extends ModuleServiceProviderBase
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    protected function discoverActions()
    {
        return [
            // \Google\Actions\OneClickGoogle::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModuleRoot()
    {
        return dirname(__DIR__);
    }

    /**
     * {@inheritdoc}
     */
    protected function getModuleName()
    {
        return 'google';
    }
}
