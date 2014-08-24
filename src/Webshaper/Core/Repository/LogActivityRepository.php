<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\LogActivity;
use Carbon\Carbon as Carbon;
class LogActivityRepository extends BaseRepository{

    function __construct(LogActivity $model){

        parent::__construct($model);
    }

    public function insertLog($loginID,$action,$type = 'API'){
        $log = new LogActivity;
        $log->txtLoginID = $loginID;
        $log->txtAction = $action;
        $log->txtType = $type;
        $log->txtItem = '';
        $log->dtCreated = Carbon::now();

        $log->save();
    }

}