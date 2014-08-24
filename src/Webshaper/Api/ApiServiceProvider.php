<?php namespace Webshaper\Api;

use Illuminate\Support\ServiceProvider;
use Webshaper\Api\Manager\ApiUserManager;

class ApiServiceProvider extends ServiceProvider{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerApiUserManager();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    public function registerApiUserManager(){
        $this->app['webshaper-apiUserManager'] = $this->app->share(function($app){

            return new ApiUserManager($this->_getModel($app,'ApiApp'),$this->_getModel($app,'ApiStoreDb'));
        });

        $this->registerAlias('ApiUserManager');
    }



    private function _getModel($app,$name){
        return $app['Webshaper\Api\Models\\'.$name];
    }

    private function registerAlias($alias){

        $this->app->booting(function() use ($alias){
            $loader =  \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias($alias,'Webshaper\Api\Support\Facades\\'.$alias);
        });
    }
} 