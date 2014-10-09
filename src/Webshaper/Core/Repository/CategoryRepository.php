<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\Category;
class CategoryRepository extends BaseRepository{

    function __construct(Category $model){
        parent::__construct($model);
    }


} 