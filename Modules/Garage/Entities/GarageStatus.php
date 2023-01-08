<?php

namespace Modules\Garage\Entities;

use Illuminate\Database\Eloquent\Model;

class GarageStatus extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function forDropdown($business_id, $include_attributes = false,$job_sheet = null)
    {
          $type =  'both';
     
        $query = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                    ->orderBy('sort_order', 'asc')
                    ->get();
       if($job_sheet){
            if($job_sheet->pay_types == 'insurance' || $job_sheet->pay_types == 'both'){
                
               $statuses =   $query->whereIn('type',['insurance','both'] )->pluck('view_name', 'id');
              
                
            }elseif($job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both'){
                
               $statuses =   $query->whereIn('type',['cash','both'] )->pluck('view_name', 'id');
                
                
            }else{
                
                
                
                    $statuses = $query->pluck('view_name', 'id');
            }
            
          
        }else{
            
                $statuses = $query->pluck('view_name', 'id');
            
            
        }
    

        //Add sms, email template as attribute
        $template_attr = null;
        if ($include_attributes) {
            $template_attr = collect($query)->mapWithKeys(function($status){
                    return [$status->id => [
                            'data-sms_template' => $status->sms_template ?? '',
                            'data-email_subject' => $status->email_subject ?? '',
                            'data-email_body' => $status->email_body ?? '',
                            'data-is_completed_status' => $status->is_completed_status
                        ]
                    ];
            })->all();
        }

        $output = ['statuses' => $statuses, 'template' => $template_attr];

        return $output;
    }
    
    
   public static function repairforDropdown($business_id, $include_attributes = false,$job_sheet = null)
    {
          $type =  'both';
     
        $query = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                    ->orderBy('sort_order', 'asc')
                    ->get();
  
                $statuses = $query->where('type','repair' )->pluck('view_name', 'id');
            
            
       
    

        //Add sms, email template as attribute
        $template_attr = null;
        if ($include_attributes) {
            $template_attr = collect($query)->mapWithKeys(function($status){
                    return [$status->id => [
                            'data-sms_template' => $status->sms_template ?? '',
                            'data-email_subject' => $status->email_subject ?? '',
                            'data-email_body' => $status->email_body ?? '',
                            'data-is_completed_status' => $status->is_completed_status
                        ]
                    ];
            })->all();
        }

        $output = ['statuses' => $statuses, 'template' => $template_attr];

        return $output;
    }

    public static function getRepairSatuses($business_id)
    {
        $list = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                        ->orderBy('sort_order', 'asc')
                        ->get();

        return $list;
    }
}
