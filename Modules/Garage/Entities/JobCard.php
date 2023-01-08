<?php

namespace Modules\Garage\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Variation;

class JobCard extends Model
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
    protected $casts = [
        'checklist' => 'array',
        'parts' => 'array',
    ];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'garage_job_cards';

    /**
     * Return the customer for the project.
     */
    public function customer()
    {
        return $this->belongsTo('App\Contact', 'contact_id');
    }
    
    /**
     * user added job sheet.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
     public function technician()
    {
        return $this->belongsTo('App\User', 'service_staff');
    }

    /**
     * technecian for job sheet.
     */
   
    /**
     * status of job sheet.
     */

  

    /**
     * get device for job sheet
     */
   

 public function company()
    {
        return $this->belongsTo(\Modules\Garage\Entities\Company::class, 'insurance_company_id');
    }
  public function status()
    {
        return $this->belongsTo(\Modules\Garage\Entities\GarageStatus::class, 'status_id');
    } 
    public function repair_status()
    {
        return $this->belongsTo(\Modules\Garage\Entities\GarageStatus::class, 'repair_status');
    }
   public function brand()
    {
        return $this->belongsTo(\Modules\Garage\Entities\CarBrand::class, 'car_brand');
    }
  

    /**
     * get business location for job sheet
     */
    public function businessLocation()
    {
        return $this->belongsTo('App\BusinessLocation', 'location_id');
    }

    /**
     * Get the repair for the job sheet
     */
    public function invoices()
    {
        return $this->hasMany('App\Transaction', 'garage_job_card_id');
    } 
    
    public function lpo()
    {
        return $this->hasOne(\Modules\Garage\Entities\Lpo::class, 'job_card_id');
    }
    
    public function invoice()
    {
        return $this->hasOne('App\Transaction', 'garage_job_card_id');
    }  
    
    public function cash_invoice()
    {
        return $this->hasOne('App\Transaction', 'garage_job_card_id')->where('garage_is_cash',1);
    } 
     public function insurance_invoice()
    {
        return $this->hasOne('App\Transaction', 'garage_job_card_id')->where('garage_is_insurance',1);
    } 
    
   

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }
    
    
        public function getPartsUsed()
    {
        $parts = [];
        if (!empty($this->parts)) {
            $variation_ids = [];
            $job_sheet_parts = $this->parts;

            foreach($job_sheet_parts as $key => $value) {
                $variation_ids[] = $key;
            } 

            $variations = Variation::whereIn('id', $variation_ids)
                                ->with(['product_variation', 'product', 'product.unit'])  
                                ->get();

            foreach ($variations as $variation) {
                $parts[$variation->id]['variation_id'] = $variation->id;
                $parts[$variation->id]['variation_name'] = $variation->full_name;
                $parts[$variation->id]['unit'] = $variation->product->unit->short_name;
                $parts[$variation->id]['unit_id'] = $variation->product->unit->id;
                $parts[$variation->id]['quantity'] = $job_sheet_parts[$variation->id]['quantity'];
            }
        }

        return $parts;
    }

}
