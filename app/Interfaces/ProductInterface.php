<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ProductInterface
{
    public function getAll();
    
    public function intialStoreData(Request &$request);
    
    public function createSingleVariation($product,$variationData,$compo);
    
    public function createProductVariation($product,$variationData,$compo);
}