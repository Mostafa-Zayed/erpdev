<?php

namespace App\Repositories;

use App\Http\Requests\StoreVariationTemplate;
use App\Http\Requests\UpdateVariationTemplate;
use App\Interfaces\VariationTemplateInterface;
use App\VariationTemplate;
use Yajra\DataTables\Facades\DataTables;
use \DB;
use App\Http\Traits\BusinessSerivce;

class VariationTemplateRepository implements VariationTemplateInterface
{

    public function store($request)
    {
        try {
            $variationTemplate = VariationTemplate::create($request->only(['name']) + ['business_id' => $this->getBusinessId()]);

            $data = [];
            foreach ($request->variation_values as $value){
                $data[] = ['name' => $value];
            }

            $variationTemplate->values()->createMany($data);

            return true;

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return false;
        }


    }

    private function getBusinessId()
    {
        return request()->session()->get('user.business_id');
    }

    public function edit($id)
    {
        return VariationTemplate::where('business_id',$this->getBusinessId())
            ->select('id','name')
            ->with([
                'values' => function($query) {
                    $query->select('name','variation_template_id');
                }
            ])->find($id);
    }

    public function update(UpdateVariationTemplate $request, $id)
    {
        // TODO: Implement update() method.
    }
    
    public function getAll($businessId = null)
    {
        $businessId = ! empty($businessId) ? $businessId : $this->getBusinessId();
        
        $variationTemplates = VariationTemplate::where('business_id', $businessId)

            ->with(
                [
                    'values' => function($query){
                        $query->select('name','variation_template_id')->get();
                    }
                ])

            ->select(
                'id',
                'name',
                DB::raw("(SELECT COUNT(id) FROM product_variations WHERE product_variations.variation_template_id=variation_templates.id) as total_pv")
            )
            
            ->get();
        
        return ! empty($variationTemplates) && $variationTemplates->count() > 0 ? $variationTemplates: collect([]);
          
    }
    
    /*
     * this function add one variation
     */
    public function add($request)
    {
        try {
            $variationTemplate = VariationTemplate::create($request->only(['name']) + ['business_id' => $this->getBusinessId()]);

            $data = [];
            foreach ($request->variation_values as $value){
                $data[] = ['name' => $value];
            }

            $variationTemplate->values()->createMany($data);

            return true;

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return false;
        }
    }
}