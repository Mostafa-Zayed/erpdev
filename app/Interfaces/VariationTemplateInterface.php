<?php

namespace App\Interfaces;

use App\Http\Requests\StoreVariationTemplate;
use App\Http\Requests\UpdateVariationTemplate;
use App\VariationTemplate;

interface VariationTemplateInterface
{
    public function store(StoreVariationTemplate $request);

    public function edit($id);

    public function update(UpdateVariationTemplate $request, $id);
    
    /*
     * this function get all variationTemplates
     */
    public function getAll($businessId = null);
    
    /*
     * this function add one variation
     */
    public function add(StoreVariationTemplate $request);
}