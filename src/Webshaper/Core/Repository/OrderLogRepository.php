<?php namespace Webshaper\Core\Repository;

use Carbon\Carbon;
use Webshaper\Core\Models\OrderLog;

class OrderLogRepository extends BaseRepository{

    function __construct(OrderLog $model){

        parent::__construct($model);
    }

    public function insertLog($orderId, $username, $desc)
    {
        $log = new OrderLog();

        $log->intPKOrder = $orderId;

        $log->txtAuthor = $username;

        $log->txtLogDesc = $desc;

        $log->dtCreated = Carbon::now();

        $log->save();
    }
} 