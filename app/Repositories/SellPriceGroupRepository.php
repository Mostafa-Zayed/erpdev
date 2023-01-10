<?php 

namespace App\Repositories;

use App\Interfaces\SellPriceGroupInterface;
use App\SellingPriceGroup;
use Illuminate\Support\Facades\DB;

class SellPriceGroupRepository implements SellPriceGroupInterface
{
    public function getAll(& $businessId)
    {
        return DB::table('selling_price_groups')->where('business_id',$businessId)->get();
    }
}