<?php namespace Webshaper\Core;

interface ProductInterface{
    public function getProductItem($intPkProduct);

    public function getBySKU($sku);
}