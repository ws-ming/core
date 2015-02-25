<?php namespace Webshaper\Core;

use Illuminate\Support\ServiceProvider;
use Webshaper\Core\Repository\CategoryRepository;
use Webshaper\Core\Repository\OrderItemRepository;
use Webshaper\Core\Repository\OrderLogRepository;
use Webshaper\Core\Repository\OrderRepository;
use Webshaper\Core\Repository\UserRepository;
use Webshaper\Core\Repository\LogActivityRepository;
use Webshaper\Core\Repository\ProductItemRepository;
use Webshaper\Core\Repository\ProductRepository;
use Webshaper\Core\Repository\CustomerRepository;
use Webshaper\Core\Support\DBHelper;
use Webshaper\Core\Support\WSHelper;

class CoreServiceProvider extends ServiceProvider {

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
        $this->registerUserRepository();
        $this->registerLogRepository();
        $this->registerProductItemRepository();
        $this->registerProductRepository();
        $this->registerCustomerRepository();
        $this->registerOrderLogRepository();
        $this->registerOrderItemRepository();
        $this->registerOrderRepository();
        $this->registerCategoryRepository();
        $this->registerWSHelper();
        $this->registerDBHelper();

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

    public function registerUserRepository(){
        $this->app['webshaper-userrepo'] = $this->app->share(function($app){

            return new UserRepository($this->_getModel($app,'User'));
        });

        $this->registerAlias('UserRepo');
    }

    public function registerLogRepository(){
        $this->app['webshaper-logrepo'] = $this->app->share(function($app){
            return new LogActivityRepository($this->_getModel($app,'LogActivity'));
        });

        $this->registerAlias('LogRepo');
    }

    public function registerProductItemRepository(){
        $this->app['webshaper-productItemRepo'] = $this->app->share(function($app){
            return new ProductItemRepository($this->_getModel($app,'ProductItem'));
        });

        $this->registerAlias('ProductItemRepo');
    }

    public function registerProductRepository(){
        $this->app['webshaper-productRepo'] = $this->app->share(function($app){
            return new ProductRepository($this->_getModel($app,'Product'));
        });

        $this->registerAlias('ProductRepo');
    }

    public function registerCustomerRepository(){
        $this->app['webshaper-customerRepo'] = $this->app->share(function($app){
            return new CustomerRepository($this->_getModel($app,'Customer'));
        });

        $this->registerAlias('CustomerRepo');
    }

    public function registerWSHelper(){
        $this->app['webshaper-wshelper'] = $this->app->share(function($app){
            return new WSHelper();
        });

        $this->registerAlias('WSHelper');
    }

    public function registerDBHelper(){
        $this->app['webshaper-dbhelper'] = $this->app->share(function($app){
            return new DBHelper();
        });

        $this->registerAlias('DBHelper');
    }

    public function registerOrderRepository(){
        $this->app['webshaper-orderRepo'] = $this->app->share(function($app){
            return new OrderRepository($this->_getModel($app,'Order'),$this->app['webshaper-orderItemRepo'],$this->app['webshaper-orderLogRepo'],$this->app['webshaper-productRepo']);
        });

        $this->registerAlias('OrderRepo');
    }

    public function registerOrderItemRepository(){
        $this->app['webshaper-orderItemRepo'] = $this->app->share(function($app){
            return new OrderItemRepository($this->_getModel($app,'OrderItem'));
        });

        $this->registerAlias('OrderItemRepo');
    }

    public function registerCategoryRepository(){
        $this->app['webshaper-categoryRepo'] = $this->app->share(function($app){
            return new CategoryRepository($this->_getModel($app,'Category'));
        });

        $this->registerAlias('CategoryRepo');
    }

    public function registerOrderLogRepository(){
        $this->app['webshaper-orderLogRepo'] = $this->app->share(function($app){
            return new OrderLogRepository($this->_getModel($app,'OrderLog'));
        });
    }

    private function _getModel($app,$name){
        return $app['Webshaper\Core\Models\\'.$name];
    }

    private function registerAlias($alias){

        $this->app->booting(function() use ($alias){
            $loader =  \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias($alias,'Webshaper\Core\Support\Facades\\'.$alias);
        });
    }

}
