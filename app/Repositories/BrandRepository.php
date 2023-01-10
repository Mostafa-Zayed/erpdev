<?php

namespace App\Repositories;

use App\Interfaces\BrandInterface;
use App\Brands;

class BrandRepository implements BrandInterface
{

    public function getAll($businessId = null)
    {
    
        if(! empty($businessId)){
            $data =  Brands::where('business_id',$businessId)->select(['name', 'description', 'id'])->get();    
        }else {
            $data = Brands::where('business_id',request()->session()->get('user.business_id'))->select(['name', 'description', 'id'])->get();
        }
        
        return ! empty($data) ? $data : collect([]);
        
        
    }
    
    public function add($brandData)
    {
        return Brands::create(self::generateStoreData($brandData));
    }
    
    private static function generateStoreData(array $storeData): array
    {
        return $storeData += [
            'business_id' => request()->session()->get('user.business_id'),
            'created_by' => request()->session()->get('user.id'),
            'slug' => str_slug($storeData['name'])
            ];
    }
}