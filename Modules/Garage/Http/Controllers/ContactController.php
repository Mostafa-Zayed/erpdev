<?php

namespace Modules\Garage\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Category;
use Illuminate\Routing\Controller;
use App\Address;
use App\Arabian\UserArabian;
use App\Cottonesta\UserCottonesta;
use App\CustomerGroup;
use App\Notifications\CustomerNotification;
use App\PurchaseLine;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\Tag;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use DB;
use App\TraficResource;
use Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ContactExport;
use App\Models\City;
use App\Models\State;
use App\Exports\LedgerExport;

class ContactController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        NotificationUtil $notificationUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }

   
   
         
            
            $input = $request->only(['type', 'supplier_business_name',
                'name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile','garage_id_number','trafic_id', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email','start_date','crm_life_stage','field','link','msg_link','fb_brief','meeting_result','crm_lead_users']);
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            
            
             $ount2 = 0;
             if(!empty($input['mobile'])) {
                 
                 
                $count2 = Contact::where('business_id', $input['business_id'])
                                ->where('mobile', $input['mobile'])
                                ->count();
                                
                
                   if($count2 > 0) {
                        
                           $output = ['success' => false,
                            'msg' =>__("messages.mobile_number_is_already_exist")
                        ];
                        
                         return $output;
                   }
            
                    
                    
             }
            
           
             if (empty($input['start_date'])) {
                    $input['start_date'] =  \Carbon::now();
                } else {
                    $input['start_date'] = $this->commonUtil->uf_date($input['start_date'], true);
                }
           
         
                if (empty($input['crm_lead_users'])) {
                $input['crm_lead_users'] =  auth()->user()->id;
            }
              
         $input2 = $request->only(["hidden-tags"]);
           if(!empty($input2["hidden-tags"])){
               $input['hidden_tags'] =  $input2["hidden-tags"] ;  
               
           }
       
           
            
            
            
            $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;

            //Check Contact id
            $count = 0;
            if (!empty($input['contact_id'])) {
                $count = Contact::where('business_id', $input['business_id'])
                                ->where('contact_id', $input['contact_id'])
                                ->count();
            }

            if ($count == 0) {
                //Update reference count
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');

                if (empty($input['contact_id'])) {
                    //Generate reference number
                    $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                }

  
         

                $contact = Contact::create($input);

                //Add opening balance
                if (!empty($request->input('opening_balance'))) {
                    $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                }

     $address =  new Address  ; 
                 $address->contact_id = $contact->id ;
                 $address->business_id = $business_id ;
                 $address->country = $contact->country ;
                 $address->city = $contact->city ;
                 $address->state = $contact->state ;
                 $address->address = $contact->landmark ;
                 $address->name = 'عنوان1 ' ;
                 $address->phone = $contact->landline ;
                 $address->mobile = $contact->mobile ;
                 $address->save() ;
                 
                 

                $output = ['success' => true,
                            'data' => $contact,
                            'msg' => __("contact.added_success")
                        ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' =>__("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
}
