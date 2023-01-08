<?php

namespace Modules\Garage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Contact;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentDeleted;
use App\Events\TransactionPaymentUpdated;
 use App\TransactionPayment;
 use App\AllLog;
 use App\Utils\TransactionUtil;
use App\Brands;
use App\BusinessLocation;
use App\Business;
use App\Transaction;
use App\Category;
use App\TaxRate;
use Modules\Garage\Entities\DeviceModel;
use Modules\Garage\Entities\GarageStatus;
use Modules\Garage\Utils\GarageUtil;
use App\Utils\Util;
use Modules\Garage\Entities\CarBrand;
use Modules\Garage\Entities\JobSheet;
use Modules\Garage\Entities\JobCard;
use Modules\Garage\Entities\Company;
use Modules\Garage\Entities\Lpo;
use App\Utils\CashRegisterUtil;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Utils\ModuleUtil;
use App\CustomerGroup;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Media;
use Spatie\Activitylog\Models\Activity;

class LpoController extends Controller
{   
    /**
     * All Utils instance.
     *
     */
    protected $repairUtil;
    protected $commonUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;
    protected $contactUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(GarageUtil $repairUtil,TransactionUtil $transactionUtil, Util $commonUtil, CashRegisterUtil $cashRegisterUtil, ModuleUtil $moduleUtil,
        ContactUtil $contactUtil, ProductUtil $productUtil)
    {
        $this->repairUtil = $repairUtil;
        $this->commonUtil = $commonUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
         $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */

   

      public function addlpo($id) {
        
         $business_id = request()->session()->get('user.business_id');
        
        $td = JobCard::where('id',$id)->first();
          $taxes = TaxRate::where('business_id', $business_id)->pluck('name', 'id');
      
          
        return view('garage::job_sheet.partials.lpo')
                ->with(compact('td','taxes'));
        
    }
   
    public function savelpo(Request $request,$id) {
        
   
     $subtotal = 0;     
     $total = 0;     
     $tax = 0;     
     
     try {   
        $td = JobCard::where('id',$id)->first();
        $user_id = request()->session()->get('user.id');
        
       $lpo =  Lpo::updateOrCreate(['job_card_id' => $id ],[
                   'lpo_no' => $request->lpo_no ,
                   'amount' => $request->amount ,
                   'excess' => $request->excess ,
                   'tax_id' => $request->tax_id ,
                   'claim_no' => $request->claim_no ,
                   'trn_no' => $request->trn_no ,
                   'lpo_date' => !empty($request->lpo_date) ? $this->commonUtil->uf_date($request->lpo_date , true) : \Carbon::now()  ,
                   'created_by' => $user_id ,
                  
                    ]);
        
    $subtotal =  $request->amount -  $request->excess ;
    
     $TaxRate =  TaxRate::find($request->tax_id);
    if(!empty($TaxRate)){
        
     $tax =  ($subtotal * $TaxRate->amount) / 100 ;  
        
    }
        
      $total =   $subtotal + $tax ;
        
     $transction = Transaction::where('garage_job_card_id',$id)->where('garage_is_insurance',1)->first();
       if($transction){
           
           $transction->update([
               'tax_id' => $request->tax_id ,
               'tax_amount' => $tax ,
               'final_total' => $total ,
               'total_before_tax' => $subtotal ,
               
               
               ]);
           
       }
        
        $td->status_id = 5;
        $td->update();
        
         activity()
            ->performedOn($td)
            ->withProperties(['update_note' => '', 'updated_status' => 'LPO_issued' ])
            ->log('status changed to LPO_issued');
        
        $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
                
    } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }   
        return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@index')
                ->with('status',$output);
        
    
    }

    
   
   
       public function addcash($id) {
        
         $business_id = request()->session()->get('user.business_id');
        
        $td = JobCard::where('id',$id)->first();
          $taxes = TaxRate::where('business_id', $business_id)->pluck('name', 'id');
      
          
        return view('garage::job_sheet.partials.cash')
                ->with(compact('td','taxes'));
        
    }
        public function savecash(Request $request,$id) {
        
   
     $subtotal = 0;     
     $total = 0;     
     $tax = 0;     
     
     try {   
        $td = JobCard::where('id',$id)->first();
        $user_id = request()->session()->get('user.id');
        
     
        
    $subtotal =  $request->amount  ;
    
     $TaxRate =  TaxRate::find($request->tax_id);
    if(!empty($TaxRate)){
        
     $tax =  ($subtotal * $TaxRate->amount) / 100 ;  
        
    }
        
      $total =   $subtotal + $tax ;
        
     $transction = Transaction::where('garage_job_card_id',$id)->where('garage_is_cash',1)->first();
       if($transction){
           
           $transction->update([
               'tax_id' => $request->tax_id ,
               'tax_amount' => $tax ,
               'final_total' => $total ,
               'total_before_tax' => $subtotal ,
               
               
               ]);
           
       }
        
      /*  $td->status_id = 5;
        $td->update();
        
         activity()
            ->performedOn($td)
            ->withProperties(['update_note' => '', 'updated_status' => 'LPO_issued' ])
            ->log('status changed to LPO_issued');
        */
        
        $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
                
      } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }   
        return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@cash',$id)
             ;
        
    
    }
    
}
