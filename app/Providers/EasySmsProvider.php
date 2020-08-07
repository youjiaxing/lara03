<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class EasySmsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EasySms::class, function ($app) {
            $smsConfig = config('easysms.config');
            if ($smsConfig['debug']) {
                $smsConfig['config']['default']['gateways'] = ['errorlog'];
            }

            return new EasySms($smsConfig);
        });
        $this->app->alias(EasySms::class, 'easysms');
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
