<?php

namespace App\Repositories;

use App\Http\Traits\BusinessService;
use App\Interfaces\UnitInterface;
use App\Unit;

class UnitRepository implements UnitInterface
{
    use BusinessService;
    
    private $columns = [
        'id',
        'actual_name',
        'short_name',
        'allow_decimal',
        'base_unit_id',
        'base_unit_multiplier'
    ];
    
    public function getAll($businessId = null, $baseOnly = false)
    {
        $query = Unit::select($this->columns);
        
        if (! empty($businessId)) {
            $query = $query->where('business_id', $this->getBusinessId());
        }

        if ($baseOnly) {
            $query = $query->whereNull('base_unit_id');
        }

        $units = $query->with(['base_unit' => function($query){
            $query->select($this->columns);
            }])->get();

        return ! empty($units) && $units->count() > 0 ? $units : collect([]);
    }

    public function getTest($businessId = null)
    {
//        $query = Unit::select()
    }
    
    
    public function getBaseOnly($businessId)
    {
        
        return $this->getAll($businessId,true);
    }
}