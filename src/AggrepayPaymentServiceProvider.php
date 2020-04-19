<?php

namespace ssi\aggrepaypayment;
use Illuminate\Support\ServiceProvider;

class AggrepayPaymentServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Aggrepay::class, function($app){
            $config = $app['config']->get('aggrepayconfig');

            if(!$config){
                throw new \RuntimeException('missing aggrepay configuration section');
            }

            if(empty($config['KEY'])){
                throw new \RuntimeException('missing aggrepay configuration: `KEY`');
            }

            if(empty($config['SALT'])){
                throw new \RuntimeException('missing aggrepay configuration: `SALT`');
            }

            if(!isset($config['MODE'])){
                throw new \RuntimeException('missing aggrepay configuration: `MODE`');
            }

            return new Aggrepay($config);
        });

        $this->app->alias(Aggrepay::class, 'aggrepay-api');
    }

    public function boot(){
        $dist = __DIR__.'/../config/aggrepayconfig.php';
        $this->publishes([
            $dist => config_path('aggrepayconfig.php'),
        ],'config');

        $this->mergeConfigFrom($dist, 'aggrepayconfig');
    }
}