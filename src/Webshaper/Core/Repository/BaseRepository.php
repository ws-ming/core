<?php namespace Webshaper\Core\Repository;

use Illuminate\Database\Eloquent\Model;
use Webshaper\Core\Exception\WSDataNotFound;

abstract class BaseRepository {

    protected $model;

    function __construct(Model $model){
        $this->model = $model;
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

    public function getOrFail($id){
        $model = $this->model->find($id);

        if(is_null($model)) throw new WSDataNotFound();

        return $model;
    }

    public function search($find, $column){
        $find = trim($find);
        $results = null;

        if(is_array($column)){

            foreach($column as $col){
                $this->model = $this->model->orWhere($col,'LIKE',"%{$find}%");
            }

            $results = $this->model->get();

        }else{
            $results =  $this->model->where($column,'LIKE',"%{$find}%")->get();
        }

        return $results->count() == 0 ? null : $results;

    }
} 