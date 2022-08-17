<?php

namespace Translate;

use App\Providers\ModuleServiceProviderBase;
use App\Support\JustInTwig;

class TranslateServiceProvider extends ModuleServiceProviderBase
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
        return [];
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
        return 'translate';
    }
}
