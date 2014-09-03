<?php namespace Webshaper\Api\Manager;

use Webshaper\Api\Models\ApiApp;
use Webshaper\Api\Models\ApiStoreDb;
use Webshaper\Api\Support\ApiError;
use Webshaper\Core\Exception\WSException;
use Webshaper\Core\Models\User;
use Webshaper\Core\Repository\UserRepository;
use Webshaper\Core\Support\DBHelper;
use Webshaper\Core\Support\ErrorCode;
use Webshaper\Core\Support\WSHelper;
use Webshaper\Core\Repository\UserRepository as UserRepo;
class ApiUserManager extends ApiBaseManager{

    protected $storeDbModel;
    function __construct(ApiApp $model,ApiStoreDb $storeDbModel){
        parent::__construct($model);
        $this->storeDbModel = $storeDbModel;
    }

    /**
     * authenticate the user, then initilize the database
     * @param $token
     * @param $storeUrl
     * @param $appName
     * @return bool
     * @throws \Webshaper\Core\Exception\WSException if the authenticate error or the store not found
     */
    public function authenticateAPIToken($token,$storeUrl,$appName){
        //check the details
        $dbHelper = new DBHelper();

        $user = $this->model->where('app_token',$token)->where('store_url',$storeUrl)->where('app_name',$appName)->first();

        if(!$user){
            throw new WSException(ApiError::AUTHENTICATE_ERROR);
        }

        $dbHelper->setupStoreDB($storeUrl);
        return true;
    }


    public function register($storeUrl, $username, $appName, $duplicate = false){
        $wsHelper = new WSHelper();
        $dbHelper = new DBHelper();
        //check the store is exists in crm
        if(!$wsHelper->isStoreExists($storeUrl)){
            throw new WSException(ErrorCode::STORE_NOT_FOUND);
        }

        //initialize the database information to store_db table
        $dbHelper->setupStoreDB($storeUrl);

        //check the user exists in the store
        $userRepo = new UserRepository(new User());
        $userRepo->getOrFail($username);

//        if(!$userRepo->authenticate($username, $password)) throw new WSException(ErrorCode::AUTHENTICATE_FAILED);

        //generate the uuid and store to app table
        $uuid = substr(md5($appName),0,10);
        $uuid = uniqid($uuid);

        $privateKey = substr(md5(time()),0,10);
        //regenerate the uuid if exists and duplicate not allow
        $userApp = $this->model->where('app_name',$appName)->where('store_url',$storeUrl)->where('owned_by',$username)->get();

        if(!$duplicate && $userApp->count() > 1){
            $userApp->each(function($user){
               $user->delete();
            });
        }else if(!$duplicate){ //not duplicated, and login before, just regenerate the token

            $app = $userApp[0];

        }else{
            $app = new ApiApp();
        }

        $app->app_name = $appName;
        $app->app_token = $uuid;
        $app->private_key = $privateKey;
        $app->store_url = $storeUrl;
        $app->owned_by = $username;
        $app->save();

        return $app;

    }
    /**
     * check if the token exists in the database
     * @param $token
     * @return bool
     */
    public function isTokenExists($token){
        $token = $this->model->where('app_token',$token)->first();
        return !is_null($token);
    }
} 