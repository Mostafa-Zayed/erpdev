<?php

namespace Modules\Garage\Entities;

use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
  
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'garage_insurance_companies';

    /**
     * Return the customer for the project.
     */
 public static function forDropdown($business_id, $show_none = false)
    {
        $brands = Company::where('business_id', $business_id)
                    ->pluck('name', 'id');

        if ($show_none) {
            $brands->prepend(__('lang_v1.none'), '');
        }

        return $brands;
    }
}
