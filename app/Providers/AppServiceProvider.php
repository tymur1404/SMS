<?php

namespace App\Providers;


use App\Services\Sms\Soap\SMSCService;
use App\Services\Sms\Soap\SoftlineService;
use App\Services\Sms\Soap\TurboSmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('TurboSmsSoapClient', function ($app) {
            return new \SoapClient(TurboSmsService::PARAM);
        });

        $this->app->singleton('SoftlineSoapClient', function ($app) {
            return new \SoapClient(SoftlineService::PARAM);
        });

        $this->app->singleton('SMSCSoapClient', function ($app) {
            return new \SoapClient(SMSCService::PARAM);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
