<?php namespace Webshaper\Core\Support;

use Webshaper\Core\Exception\WSException;
use Webshaper\Crm\Models\CrmTrialWebsite;
use Webshaper\Crm\Models\CrmClientWebsite;

class WSHelper {

    /**
     * to show the json error message during convertion.
     * Source: http://php.net/manual/en/function.json-last-error.php
     * @param  int $errorNum error number of json
     * @return string           error message with prefix "Json error:" and following by the message
     */
    private function getJsonErrorMsg($errorNum){
        $prefix = 'Json error :';
        switch ($errorNum){
            case 1:
                $prefix.='The maximum stack depth has been exceeded';
                break;
            case 2:
                $prefix.='Invalid or malformed JSON';
                break;
            case 3:
                $prefix.='Control character error, possibly incorrectly encoded';
                break;
            case 4:
                $prefix.='Syntax error';
                break;
            case 5:
                $prefix.='Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case 6:
                $prefix.='One or more recursive references in the value to be encoded';
                break;
            case 7:
                $prefix.='One or more NAN or INF values in the value to be encoded ';
                break;
            case 8:
                $prefix.='A value of a type that cannot be encoded was given';
                break;
        }
        return $prefix;
    }




    /**
     * @param $jsonString
     * @return JSON Object
     * @throws CoreException
     */
    public function isJson($jsonString){
        $json = json_decode($jsonString);
        if(json_last_error() != 0){
            throw new WSException($this->getJsonErrorMsg(json_last_error()));
        }else{
            return $json;
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function json($data){
        $response = \Response::json($data);
        $response->header('Content-Type', 'application/json');
        $response->header('charset', 'utf-8');
        return $response;
    }

    /**
     * @param $oldDate
     * @return string
     */
    public function formatDateToTimeZone($oldDate){
        $date = new \DateTime($oldDate);
        $date->modify('+'. intval($this->getTimeZone()).' hours');
        return $date->format('Y-m-d h:i a');
    }


    /**
     * to get rid of www prefix in URL if it is exists
     * 	@param string url of store
     * @return string the url of store without www prefix
     * */
    public function cleanDomain($url){
        $pattern = '/\w+\..{2,3}(?:\..{2,3})?(?:$|(?=\/))/i';

        if (preg_match($pattern, $url, $matches) === 1) {
            return $matches[0];
        }
    }

    /**
     * check the trial store is in the record
     * @param $trialStoreName
     * @return bool
     */
    public function isTrialExists($trialStoreName){
        //check the trial store is exists
        $trialStore = CrmTrialWebsite::on('webshaper-crm')->where('freetrialfolder',$trialStoreName)->first();
        if(is_null($trialStore)){
            return false;
        }

        return true;
    }

    /**
     * check whether the name is "looks" like trial store name
     * @param $trialStoreName
     * @return bool
     */
    public function isTrialStoreName($trialStoreName){
        //assume invalid url is trial store name
        if($this->validateUrl($trialStoreName)){
           return false;
        }

        return true;
    }

    /**
     * check the live store is exists
     * @param $storeUrl
     * @return bool
     */
    public function isLiveStoreExists($storeUrl){

        $liveStore = CrmClientWebsite::on('webshaper-crm')->where('domain',$storeUrl)->first();


        if(is_null($liveStore)) return false;

        return true;
    }

    /**
     * check the store exists in live store or trial store
     * @param $storeUrl
     * @return bool
     */
    public function isStoreExists($storeUrl){
        $storeUrl = $this->cleanDomain($storeUrl);

        if($this->isTrialStoreName($storeUrl)){
            if(!$this->isTrialExists($storeUrl)) return false;
        }

        if(!$this->isLiveStoreExists($storeUrl)) return false;

        return true;
    }
    /**
     * check the given string is validate url
     * @param $storeUrl
     * @return bool true if the string is url
     */
    public function validateUrl($storeUrl){
        $pattern = "@^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$@i";
        $storeUrl = strtolower($storeUrl);

        return preg_match($pattern,$storeUrl) == 0 ? false : true;
    }
} 