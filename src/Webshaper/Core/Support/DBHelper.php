<?php namespace Webshaper\Core\Support;

use Webshaper\Core\Exception\WSException;
use Webshaper\Crm\Models\CrmClientWebsite as ClientWebsite;
use Webshaper\Crm\Models\CrmTrialWebsite as TrialWebsite;
use Webshaper\Crm\Models\CrmDBMapping as DBMapping;
use Webshaper\Api\Models\ApiStoreDb;
class DBHelper {

    private $LINUX_DB_HOST = '210.5.43.222';
    private $SERVER_4_DB_HOST = '183.81.160.79';
    private $DEBUG = TRUE;

    /**
     * setup the database
     * @param $storeUrl
     * @throws \Webshaper\Core\Exception\WSException if the store not found
     */
    public function setupStoreDB($storeUrl){

        $storeDB = $this->getStoreDBInfoFromLocal($storeUrl);

        if($storeDB){
            $this->setupDBConfig($storeDB->db_name, $storeDB->db_username, $storeDB->db_password, $storeDB->db_host);
            return;
        }

        if(!$storeDB){
           $storeDB =  $this->initializeStoreDB($storeUrl);
        }

        if(is_null($storeDB)) throw new WSException(ErrorCode::SETUP_STORE_NOT_FOUND);

        $this->setupDBConfig($storeDB->dbname, $storeDB->dbuser, $storeDB->dbpw, $storeDB->dbhost);
    }

    /**
     * initialize the store database information to the "caching" database
     * @param $storeUrl
     */
    public function initializeStoreDB($storeUrl){

        $storeDB = $this->getStoreDBInfoFromRemote($storeUrl);
        if($storeDB){
            $this->saveStoreDBToLocal($storeUrl, $storeDB);
        }

        return $storeDB;

    }
    /**
     * get the store database information from crm records or through API
     * @param $storeUrl
     * @return mixed|null
     * @throws \Webshaper\Core\Exception\WSException
     */
    public function getStoreDBInfoFromRemote ($storeUrl){
        $helper = new WSHelper;
        $storeUrl = $helper->cleanDomain($storeUrl);

        if($helper->isTrialStoreName($storeUrl)){
            if(!$helper->isTrialExists($storeUrl)) throw new WSException(ErrorCode::TRIAL_STORE_NOT_EXISTS);
            $storeDB = $this->getTrialStoreDBInfo($storeUrl);
        }else{
            $storeDB = $this->getLiveStoreDBInfo($storeUrl);
        }

        return $storeDB;
    }

    /**
     * get the store database information from own local database table which created in this package
     * @param $storeUrl
     * @return mixed
     */
    public function getStoreDBInfoFromLocal ($storeUrl){
        $storeDB = ApiStoreDb::where('store_url',$storeUrl)->first();
        //require to change the format to match webShaper store column name

        return $storeDB;
    }

    /**
     * save the store database information to the local database for a caching purpose
     * @param $storeUrl
     * @param $storeDB
     */
    public function saveStoreDBToLocal($storeUrl, $storeDB){
        $_storeDB = new ApiStoreDb();

        $_storeDB->store_url = $storeUrl;
        $_storeDB->db_host = $storeDB->dbhost;
        $_storeDB->db_name = $storeDB->dbname;
        $_storeDB->db_username = $storeDB->dbuser;
        $_storeDB->db_password = $storeDB->dbpw;


        $_storeDB->save();
    }

    /**
     * get the live store database information from CRM / API
     * @param $storeUrl
     * @return mixed|null
     */
    public function getLiveStoreDBInfo($storeUrl){
        //get the store information
        $columns = array('mapid','serverid');
        $clientWebsite = ClientWebsite::where('domain',$storeUrl)->first($columns);

        foreach($columns as $col){
            if(empty($clientWebsite->$col)) return null;
        }
        //check if the store is server 4
        if($this->isLinuxDBServer($clientWebsite->serverid)){
            $websiteDB = DBMapping::find($clientWebsite->mapid,array('dbname','dbuser','dbpw'));
            $websiteDB->dbhost = $this->LINUX_DB_HOST;
        }else{
            $websiteDB = $this->getServer4DBInfo($storeUrl);
            $websiteDB->dbhost = $this->SERVER_4_DB_HOST;
        }

        return $websiteDB;
    }

    /**
     * get the trial store database information from CRM
     * @param $trialName
     * @return null
     */
    public function getTrialStoreDBInfo($trialName){
        //serverid for trialstore is 6
        $trialWebsite = TrialWebsite::where('freetrialfolder',$trialName)->first(array('mapid'));

        if(is_null($trialWebsite)) return null;

        $trialWebsiteDB = DBMapping::find($trialWebsite->mapid,array('dbname','dbuser','dbpw'));
        $trialWebsiteDB->dbhost = $this->LINUX_DB_HOST;

        return $trialWebsiteDB;
    }

    /**
     * get the server 4 Database information through the API, since the server 4 database is located at server 4 localhost
     * @param $storeURL
     * @return mixed
     */
    private function getServer4DBInfo($storeURL){
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $storeURL.'/api/siteAPI.asp?k=thi2@^^@$$&todo=requestDBdetails');

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        $output = explode('|',$output);
        $info = new \stdClass();
        $info->dbname = $output[0];
        $info->dbuser = $output[1];
        $info->dbpw = $output[2];

        return $info;
    }

    /**
     * @param $serverID
     * @return bool
     */
    private function isLinuxDBServer($serverID){
        if ($serverID == 8){ //8 is server 4 server id
            return false;
        }
        return true;
    }

    /**
     * does the db info has been stored in "caching" table?
     * @param $storeUrl
     */
    public function isStoreDBExists($storeUrl){
        $storeDB = ApiStoreDb::where('store_url',$storeUrl)->first();

        if(is_null($storeDB)){
            return false;
        }

        return true;
    }
    /**
     * setup the laravel database configuration
     * @param $dbName
     * @param $dbUser
     * @param $dbPwd
     */
    public function setupDBConfig($dbName, $dbUser, $dbPwd, $dbhost){
        if($this->DEBUG) $dbhost = 'localhost';
        \Config::set('database.connections.webshaper-tenant.host', $dbhost);
        \Config::set('database.connections.webshaper-tenant.database',$dbName);
        \Config::set('database.connections.webshaper-tenant.username',$dbUser);
        \Config::set('database.connections.webshaper-tenant.password',$dbPwd);
        \Config::set('database.connections.webshaper-tenant.charset','utf8');
        \Config::set('database.connections.webshaper-tenant.collation','utf8_unicode_ci');
        \Config::set('database.connections.webshaper-tenant.prefix','');

    }

}