<?php 

namespace App\Repositories;

use App\Interfaces\WarrantyInterface;
use App\Warranty;
use App\Http\Traits\BusinessService;

class WarrantyRepository implements WarrantyInterface
{
    use BusinessService;
    
    public function add($brandData)
    {
        return Warranty::create(self::generateStoreData($brandData));
    }
    
    private static function generateStoreData(array $inputs): array
    {
        return $inputs += [
            'business_id' => BusinessService::getBusinessId(),
            'created_by' => BusinessService::getUser(),
            'slug' => str_slug($inputs['name'])
            ];
    }
}
