<?php

namespace AgenceCyberial\GraphqlClient;

use Illuminate\Support\ServiceProvider;
use AgenceCyberial\GraphqlClient\Classes\Client;
use AgenceCyberial\GraphqlClient\Classes\Factory;

class GraphqlClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('graphqlClient', function($app) {
            return new Factory();
        });
        
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'graphqlclient');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {  
        if ($this->app->runningInConsole()){
            $this->publishes([
            __DIR__.'/../config/config.php' => config_path('graphqlclient.php'),
            ], 'config');
        }
    }
}
