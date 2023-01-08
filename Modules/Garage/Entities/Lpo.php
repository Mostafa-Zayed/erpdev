<?php

namespace Modules\Garage\Entities;

use Illuminate\Database\Eloquent\Model;


class Lpo extends Model
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
    protected $table = 'garage_lpo';

    /**
     * Return the customer for the project.
     */
     


 public function job_card()
    {
        return $this->belongsTo(\Modules\Garage\Entities\JobCard::class, 'job_card_id');
    }
    
 

}
