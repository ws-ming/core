<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\Category;
use Webshaper\Core\Models\ProductItem;
class CategoryRepository extends BaseRepository{

    function __construct(Category $model){
        parent::__construct($model);
    }

    public function getAllMain()
    {
        $distinctCatId = ProductItem::distinct('intPKCategory')->get(array('intPKCategory'))->toArray();
        $categories = Category::where('intActive',1)->whereIn('intPKCategory',$distinctCatId)->get();

        return $categories;
    }

    public function getLastModified()
    {
        $category = Category::limit(1)->orderBy('dtModified','desc')->first();

        return $category;
    }
} 