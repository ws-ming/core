<?php namespace Webshaper\Api\Manager;

use Illuminate\Database\Eloquent\Model;

abstract class ApiBaseManager {

    private $CONNECTION = 'webshaper-api';

    protected $model;

    function __construct(Model $model){
        $this->model = $model->on($this->CONNECTION);
    }

    public function getAll($start=0, $limit=0){
        if($limit === 0){
            return $this->model->get();
        }else{
            return $this->model->skip($start)->take($limit)->get();
        }
    }

    public function get($id){
        return $this->model->find($id);
    }

}