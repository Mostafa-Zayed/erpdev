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
use Modules\Garage\Entities\DeviceModel;
use Modules\Garage\Entities\GarageStatus;
use Modules\Garage\Utils\GarageUtil;
use App\Utils\Util;
use Modules\Garage\Entities\CarBrand;
use Modules\Garage\Entities\JobSheet;
use Modules\Garage\Entities\JobCard;
use Modules\Garage\Entities\Company;
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
use Image;

class JobSheetController extends Controller
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
    public function index()
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && (auth()->user()->can('garage.view'))) )) {
            abort(403, 'Unauthorized action.');
        }

        $is_user_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);

        if (request()->ajax()) {
            
            $job_sheets = JobCard::with('invoices')
                    ->leftJoin('contacts', 'garage_job_cards.contact_id', '=', 'contacts.id')
                    ->leftJoin(
                        'garage_statuses AS rs',
                        'garage_job_cards.status_id',
                        '=',
                        'rs.id'
                    ) ->leftJoin(
                        'garage_statuses AS rrs',
                        'garage_job_cards.repair_status',
                        '=',
                        'rrs.id'
                    )
                    ->leftJoin('users as technecian', 'garage_job_cards.service_staff', '=', 'technecian.id')
                 
                    ->leftJoin(
                        'garage_car_brands AS b',
                        'garage_job_cards.car_brand',
                        '=',
                        'b.id'
                    )
                    ->leftJoin(
                        'business_locations AS bl',
                        'garage_job_cards.location_id',
                        '=',
                        'bl.id'
                    )
                    ->leftJoin(
                        'garage_insurance_companies as device',
                        'device.id',
                        '=',
                        'garage_job_cards.insurance_company_id'
                    )
                    ->leftJoin(
                        'garage_lpo as lpo',
                        'lpo.job_card_id',
                        '=',
                        'garage_job_cards.id'
                    ) 
                    
                    ->leftJoin(
                        'tax_rates as tax',
                        'tax.id',
                        '=',
                        'lpo.tax_id'
                    )
                    ->leftJoin('users', 'garage_job_cards.created_by', '=', 'users.id')
                    ->where('garage_job_cards.business_id', $business_id)
                    ->select(
                    'job_sheet_no', 
                    'job_card_photo', 
                    DB::raw("CONCAT(COALESCE(technecian.surname, ''),' ',COALESCE(technecian.first_name, ''),' ',COALESCE(technecian.last_name,'')) as technecian"),
                    DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as added_by"),
                    'contacts.name as customer',
                    'contacts.mobile as mobile',
                    'b.name as brand', 
                   
                    'serial_no', 
                    'insurance_cost', 
                    'cash_cost', 
                    'garage_job_cards.amount', 
                    'repair_status', 
                    'rs.view_name as status', 
                    'rrs.view_name as re_status', 
                    'garage_job_cards.id as id', 
                    'garage_job_cards.car_status', 
                    'garage_job_cards.pay_types', 
                    'garage_job_cards.type', 
                    'garage_job_cards.deposit', 
                    'garage_job_cards.car_plate', 
                    'garage_job_cards.completed_on', 
                    'garage_job_cards.date_in', 
                    'garage_job_cards.care_model', 
                    'garage_job_cards.repair_days', 
                    'garage_job_cards.created_at as created_at',
                  
                    'rs.color as status_color',
                    'rrs.color as re_status_color',
                    'bl.name as location',
                    'rs.is_completed_status',
                    'lpo.lpo_no',
                    'lpo.amount as lpo_amount',
                    'lpo.excess',
                    'lpo.claim_no',
                    'tax.amount as tax_amount',
                   
                    'device.name as device');

            //if user is not admin get only assgined/created_by job sheet
            if (!auth()->user()->can('job_sheet.view_all')) {
                if (!$is_user_admin) {
                    $user_id = auth()->user()->id;
                    $job_sheets->where(function ($query) use ($user_id){
                        $query->where('garage_job_cards.service_staff', $user_id)
                            ->orWhere('garage_job_cards.created_by', $user_id);
                    });
                }
            }

            //if location is not all get only assgined location job sheet
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $job_sheets->whereIn('garage_job_cards.location_id', $permitted_locations);
            }

            //filter location
            if (!empty(request()->get('location_id'))) {
                $job_sheets->where('garage_job_cards.location_id', request()->get('location_id'));
            }
            
            //filter by customer
            if (!empty(request()->contact_id)) {
                $job_sheets->where('garage_job_cards.contact_id', request()->contact_id);
            }

            //filter by technecian
            if (!empty(request()->technician)) {
                $job_sheets->where('garage_job_cards.service_staff', request()->technician);
            }

            //filter by status
            if (!empty(request()->status_id)) {
                $job_sheets->where('garage_job_cards.status_id', request()->status_id);
            }
 if (!empty(request()->car_plate)) {
     
     $car_plate = request()->car_plate ;
        $job_sheets->where(function ($query) use ($car_plate){
                        $query->where('garage_job_cards.car_plate' , 'like', '%' . $car_plate .'%' )
                            ->orWhere('contacts.mobile', $car_plate);
                    });
             //   $job_sheets->where('garage_job_cards.car_plate' , 'like', '%' . request()->car_plate .'%' );
            } 
            
            //filter out mark as completed status
            $job_sheets->where('rs.is_completed_status', request()->get('is_completed_status'));

            return DataTables::of($job_sheets)
                ->addColumn('action', function($row) {
                    $html = '<div class="btn-group">
                                <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                    '.__("messages.action").'
                                    <span class="caret"></span>
                                    <span class="sr-only">
                                    '.__("messages.action").'
                                    </span>
                                </button>';

                    $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can("garage.view")) {
                            $html .= '<li>
                                <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@show', ['id' => $row->id]) . '" class="cursor-pointer"><i class="fa fa-eye"></i> '.__("messages.view").'
                                </a>
                                </li>';
                    }

                  

                    if (auth()->user()->can("garage.update")) {
                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@edit', ['id' => $row->id]) . '" class="cursor-pointer edit_job_sheet"><i class="fa fa-edit"></i> '.__("messages.edit").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addParts', ['id' => $row->id]) . '" class="cursor-pointer">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_parts").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@getUploadDocs', ['id' => $row->id]) . '" class="cursor-pointer">
                                        <i class="fas fa-file-alt"></i>
                                        '.__("garage::lang.upload_docs").'
                                    </a>
                                </li>';
                    }


                    $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@print', ['id' => $row->id]) . '" target="_blank"><i class="fa fa-print"></i> '.__("messages.print").'
                                    </a>
                                </li>';

                    if (auth()->user()->can("garage.update")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editStatus', ['id' => $row->id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_status").'
                                    </a>
                                </li>';    
                                
                              
                    }
                         
                  /*  if (auth()->user()->can("job_sheet.delete")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@destroy', ['id' => $row->id]) . '"  id="delete_job_sheet" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>'; 
                                
                              
                    }*/
                    
                       if (auth()->user()->can("garage.update")) {
                            $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addphoto', ['id' => $row->id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.jobcard_photo").'
                                    </a>
                                </li>';  
                        
                        if($row->pay_types == "both" || $row->pay_types == "insurance"){        
                      $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\LpoController@addlpo', ['id' => $row->id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_lpo").'
                                    </a>
                                </li>';
                        }
                         $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editRepairStatuses', ['id' => $row->id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_repair_status").'
                                    </a>
                                </li>';   
                                
                                     
                   if($row->pay_types == "both" || $row->pay_types == "insurance"){        
                           
                      $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@insurance', ['id' => $row->id]) . '" class="cursor-pointer print_insurance"  target="_blank">
                                        <i class="fa fa-print"></i>
                                        '.__("garage::lang.print_insurance").'
                                    </a>
                                </li>';   
                                
                       }             
                       if($row->pay_types == "both" || $row->pay_types == "cash"){             
                      $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\LpoController@addcash', ['id' => $row->id]) . '" class="cursor-pointer btn-modal"  >
                                        <i class="fa fa-print"></i>
                                        '.__("garage::lang.print_cash").'
                                    </a>
                                </li>';  
                                
                       }  
                       
                       $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@car_work', ['id' => $row->id]) . '" class="cursor-pointer print_insurance"  target="_blank">
                                        <i class="fa fa-print"></i>
                                        '.__("garage::lang.car_work").'
                                    </a>
                                </li>';  
                                
                           
                       }  
                    $html .= '</ul>
                            </div>';
                    return $html;
                })  ->addColumn(
                    'actions',
                    '@can("garage.update")
                        <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    @endcan
                    @if (auth()->user()->can("garage.view") ) 
                    <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
                    &nbsp;
                    @endif
                    @can("garage.update") 
                    @if($pay_types == "both" || $pay_types == "insurance")
                        <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@create_estimation\', [$id])}}" class="btn btn-xs btn-primary "><i class="glyphicon glyphicon-list"></i> @lang("garage::lang.estimation")</a>
                      @endif 
                          @endcan'
                )
                ->editColumn('completed_on', 
                    '
                        @if($completed_on)
                            {{@format_datetime($completed_on)}}
                        @endif
                    '
                )
                ->editColumn('created_at', 
                    '
                      {{@format_datetime($created_at)}}
                   
                    
                    '
                )
                ->editColumn('pay_types', function($row){
                    return __('garage::lang.'.$row->pay_types);
                }) 
                ->editColumn('car_status', function($row){
                    
                    $color = $row->car_status == 'in' ? 'green' : 'red';
                    
                    $html = '<a data-href="' . action("\Modules\Garage\Http\Controllers\JobSheetController@editCarStatus", [$row->id]) . '" class="edit_job_sheet_status cursor-pointer" data-orig-value="'.$row->car_status.'" data-status-name="'.$row->car_status.'">
                                <span class="label " style="background-color:'.$color.';" >
                                    ' .__('garage::lang.'.$row->car_status) .'
                                </span>
                            </a>
                        ';
                    return $html;
                    
                })
                ->editColumn('estimated_cost', function($row){
                     $total = $row->amount   ;
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                })  
                
                ->editColumn('lpo_amount', function($row){
                     $total = $row->lpo_amount   ;
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                })  
                ->editColumn('excess', function($row){
                     $total = $row->excess   ;
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                }) 
                ->editColumn('deposit', function($row){
                     $total = $row->deposit   ;
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                })  
                ->editColumn('tax_amount', function($row){
                    
                     $total = (( $row->lpo_amount -   $row->excess) *  $row->tax_amount) / 100  ;
                     
                     
                     
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                }) 
                 ->addColumn('lpo_balance', function($row){
                    
                     $total =  ( $row->lpo_amount -   $row->excess) + ( (( $row->lpo_amount -   $row->excess) *  $row->tax_amount) / 100 ) - $row->deposit    ;
                     
                     
                     
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                }) 
                 ->addColumn('cash_work', function($row){
                    
                     $total = $row->pay_types == 'cash' ||  $row->pay_types == 'both' ? 'yes' : ''   ;
                     
                     
                     
                   
                    
                    return $total;
                }) 
                
                ->editColumn('amount', function($row){
                     $total = $row->amount   ;
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $total . '">' .$total . '</span>';
                    
                    return $cost;
                })
                ->editColumn('repair_no', function($row) {
                    $invoice_no = [];
                    if ($row->invoices->count() > 0) {
                        foreach ($row->invoices as $key => $invoice) {
                            $invoice_no[] = $invoice->invoice_no;
                        }
                    }

                    $add_invoice = '';
                    if (auth()->user()->can("repair.create")) {
                        $add_invoice = '<br><a href="' . action('SellPosController@create'). '?sub_type=repair&job_sheet_id='.$row->id. '" class="cursor-pointer" data-toggle="tooltip" title="'.__('garage::lang.add_invoice').'">
                                <i class="fas fa-plus-circle"></i>
                            </a>';
                    }

                    return implode(', ', $invoice_no) . $add_invoice;
                }) 
                
                ->editColumn('repair_days', function($row) {
                    $repair_days = '';
                  if(!empty($row->repair_days)){
                      
                      $repair_days = date('Y-m-d', strtotime($row->created_at. ' + '.$row->repair_days.' days'));
                      
                  }

                    return $repair_days;
                })
                ->editColumn('status', function($row) {
                    $html = '<a data-href="' . action("\Modules\Garage\Http\Controllers\JobSheetController@editStatus", [$row->id]) . '" class="edit_job_sheet_status cursor-pointer" data-orig-value="'.$row->status.'" data-status-name="'.$row->status.'">
                                <span class="label " style="background-color:'.$row->status_color.';" >
                                    ' .$row->status .'
                                </span>
                            </a>
                        ';
                    return $html;
                }) 
                ->editColumn('re_status', function($row) {
                    $html = '<a data-href="' . action("\Modules\Garage\Http\Controllers\JobSheetController@editRepairStatuses", [$row->id]) . '" class="edit_job_sheet_status cursor-pointer" data-orig-value="'.$row->re_status.'" data-status-name="'.$row->re_status.'">
                                <span class="label " style="background-color:'.$row->re_status_color.';" >
                                    ' .$row->re_status .'
                                </span>
                            </a>
                        ';
                    return $html;
                }) 
                ->editColumn('job_sheet_no', function($row) {
                  
                    $invoice_no = $row->job_sheet_no;
                    
                   if (!empty($row->job_card_photo)) {
                    $invoice_no .= ' &nbsp;<small class="label bg-green label-round no-print" style="font-size: 10px" title="' . __('garage::lang.jobcard_photo') .'"><i class="fas fa-check-circle"></i></small>';

                    }   
                  
                  
                   return $invoice_no;
                })
                ->removeColumn('id')
                ->rawColumns(['action','actions',  'created_at', 'completed_on','pay_types', 'repair_no','repair_days', 'status', 're_status', 'cash_work', 'estimated_cost', 'lpo_balance', 'car_status', 'job_sheet_no', 'amount', 'tax_amount', 'deposit', 'excess', 'lpo_amount'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $status_dropdown = GarageStatus::forDropdown($business_id);
        $service_staffs = $this->commonUtil->serviceStaffDropdown($business_id);

        $user_role_as_service_staff = auth()->user()->roles()
                            ->where('is_service_staff', 1)
                            ->get()
                            ->toArray();
        $is_user_service_staff = false;
        if (!empty($user_role_as_service_staff) && !$is_user_admin) {
            $is_user_service_staff = true;
        }

        return view('garage::job_sheet.index')
            ->with(compact('business_locations', 'customers', 'status_dropdown', 'service_staffs', 'is_user_service_staff'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {   
        
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.create')) || (auth()->user()->can('garage.create')))) {
            abort(403, 'Unauthorized action.');
        }

        $repair_statuses = GarageStatus::getRepairSatuses($business_id);
        $device_models = DeviceModel::forDropdown($business_id);
        $brands = CarBrand::forDropdown($business_id, false, true);
        $devices = Company::forDropdown($business_id);
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
        if ($this->commonUtil->isModuleEnabled('service_staff')) {
            $technecians = $this->commonUtil->serviceStaffDropdown($business_id);
        }

        return view('garage::job_sheet.create')
            ->with(compact('repair_statuses', 'device_models', 'brands', 'devices', 'default_status', 'technecians', 'business_locations', 'types', 'customer_groups', 'walk_in_customer', 'repair_settings'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.create'))|| auth()->user()->can('garage.create'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('contact_id', 'car_status', 'serial_no', 'notes', 'status_id', 'deposit', 'repair_days', 'amount', 'completed_on', 'date_in', 'excess',  'insurance_company_id', 'type', 'pay_types', 'service_staff', 'location_id', 'car_brand', 'car_plate', 'care_model',  'cash_desc',  'insurance_desc',  'cash_cost',  'insurance_cost', 'comment_by_ss', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5');

      ini_set('max_execution_time', 20000);
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '50M');
        ini_set('max_input_vars', '2000');
        ini_set('max_input_time', '2000');

            if($input['pay_types'] == 'cash' ){
                
           if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = 0;
               
            } 
            
            $input['insurance_company_id'] = null;
            if (!empty($input['cash_cost'])) {
                $input['cash_cost'] = $this->commonUtil->num_uf($input['cash_cost']);
            }
            
            if (!empty($input['deposit'])) {
                $input['deposit'] = $this->commonUtil->num_uf($input['deposit']);
            }
  
                
            }elseif($input['pay_types'] == 'excess'){
                
             if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = $this->commonUtil->num_uf($input['insurance_cost']);
            }
            if (!empty($input['cash_cost'])) {
               
                $input['cash_cost'] = 0;
            }  
            if (!empty($input['deposit'])) {
               
                $input['deposit'] = 0;
            }
  
                 $input['pay_types'] = 'insurance';
                 $input['status_id'] = 2;
            }else{
                
                
            if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = $this->commonUtil->num_uf($input['insurance_cost']);
            }
            if (!empty($input['cash_cost'])) {
                $input['cash_cost'] = $this->commonUtil->num_uf($input['cash_cost']);
            }
  if (!empty($input['deposit'])) {
                $input['deposit'] = $this->commonUtil->num_uf($input['deposit']);
            }


            $input['status_id'] = 2;
            }


            if (!empty($input['completed_on'])) {
                $input['completed_on'] = $this->commonUtil->uf_date($input['completed_on'], true);
            } 
            
            if (!empty($input['date_in'])) {
                $input['date_in'] = $this->commonUtil->uf_date($input['date_in'], true);
            }

         
           
           if(!empty($request->new_contact) && $request->new_contact == 1){
               
               
            $contact = Contact::updateOrCreate(['mobile' => $request->mobile , 'business_id' => $business_id],[
                   'name' => $request->name ,
                   'type' => 'customer' ,
                   'created_by' => $user_id ,
                    ]);
               
               
               $input['contact_id'] = $contact->id;
               
           }
           
           if($input['car_status'] == 'in'){
                 $input['date_in'] = \Carbon::now() ;  
           }

            DB::beginTransaction();

            //Generate reference number
            $ref_count = $this->commonUtil->setAndGetReferenceCount('job_sheet', $business_id);
            $business = Business::find($business_id);
            $repair_settings = json_decode($business->garage_settings, true);

            $job_sheet_prefix = '';
            if (isset($repair_settings['job_sheet_prefix'])) {
                $job_sheet_prefix = $repair_settings['job_sheet_prefix'];
            }

            $input['job_sheet_no'] = $this->commonUtil->generateReferenceNumber('job_sheet', $ref_count, null, $job_sheet_prefix);

            $input['created_by'] = $user_id;
            $input['business_id'] = $business_id;


                $image_64 = $request->car_diagram; //your base64 encoded data
                
                  $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
                
                  $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
                
                // find substring fro replace here eg: data:image/png;base64,
                
                 $image = str_replace($replace, '', $image_64); 
                
                 $image = str_replace(' ', '+', $image); 
                
                 $imageName = Str::random(10).'.'.$extension;
                
                $image = base64_decode($image) ;
                
               // dd($image);
             //    \Storage::disk('public')->put($imageName,$image );
             
             file_put_contents('uploads/job_card/'.$imageName, $image);
             
             
             
          //   $image->storeAs('uploads/job_card', $imageName);
                 
            $input['car_marks'] = $imageName ;
            
            
            
            
            
       /*   if(!empty($request->police_report)){
              
              
                
                $police_report = time() . '_' . $request->police_report->getClientOriginalName();
                if ($request->police_report->storeAs('job_card/', $police_report)) {
                    $input['police_report']  = $police_report;
                }  
            }*/
            
            
        if ($request->hasFile('police_report')) {
            $files = $request->file('police_report');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['police_report'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
            
              if ($request->hasFile('id_photo')) {
            $files = $request->file('id_photo');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['id_photo'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
              if ($request->hasFile('d_license')) {
            $files = $request->file('d_license');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['d_license'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
              if ($request->hasFile('v_license')) {
            $files = $request->file('v_license');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['v_license'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
            
            
            
           /* 
              if(!empty($request->id_photo)){
                
                  $id_photo = time() . '_' . $request->id_photo->getClientOriginalName();
                if ($request->id_photo->storeAs('job_card/', $id_photo)) {
                    $input['id_photo']  = $id_photo;
                }  
            }
          
                
                  if(!empty($request->d_license)){
                
                 $d_license = time() . '_' . $request->d_license->getClientOriginalName();
                if ($request->d_license->storeAs('job_card/', $d_license)) {
                    $input['d_license']  = $d_license;
                }
            }
              
                
                  if(!empty($request->v_license)){
                
                  $v_license = time() . '_' . $request->v_license->getClientOriginalName();
                if ($request->v_license->storeAs('job_card/', $v_license)) {
                    $input['v_license']  = $v_license;
                }
            
            }
            */
            
            $job_sheet = JobCard::create($input);

            //upload media
            Media::uploadMedia($business_id, $job_sheet, $request, 'images');

            if (!empty($request->input('send_notification')) && in_array('sms', $request->input('send_notification'))) {
                $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                            ->find($job_sheet->status_id);
                if (!empty($status->sms_template)) $this->repairUtil->sendJobSheetUpdateSmsNotification($status->sms_template, $job_sheet); 
            }

            if (!empty($request->input('send_notification')) && in_array('email', $request->input('send_notification'))) {
                $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                            ->find($job_sheet->status_id);
                $notification = [
                        'subject' => $status->email_subject,
                        'body' => $status->email_body
                    ];
                if (!empty($status->email_subject) && !empty($status->email_body)) $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
            }
            
            
            
           if($job_sheet->pay_types == 'cash') {
               
               
                         
          $transaction =  Transaction::create([
                'business_id' => $business_id,
                'location_id' => $job_sheet->location_id,
                'type' => 'job_card',
                'status' => 'final',
                'payment_status' => 'due',
                'contact_id' => $job_sheet->contact_id,
                'transaction_date' => $job_sheet->created_at,
                'invoice_no' => $job_sheet->job_sheet_no,
                'total_before_tax' => $job_sheet->cash_cost ,
                'final_total' => $job_sheet->cash_cost ,
                'garage_total_cash' => $job_sheet->cash_cost ,
                'garage_cash' => $job_sheet->cash_cost ,
                'created_by' => $job_sheet->created_by,
                'garage_job_card_id' => $job_sheet->id,
                'garage_is_cash' => 1,
                
                
                ]);
            
        
          
          
          if(!empty($job_sheet->deposit)){
              
              
                $inputs['paid_on'] = $transaction->transaction_date;
                $inputs['transaction_id'] = $transaction->id;
                $inputs['amount'] = $this->transactionUtil->num_uf($job_sheet->deposit);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['papers'] = 0;
                $inputs['payment_for'] = $transaction->contact_id;

            
                 $inputs['method'] = 'cash';

              

              
              $prefix_type = 'sell_payment';
             

               

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
              
                 $tp = TransactionPayment::create($inputs);
                
                //update payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                $inputs['transaction_type'] = $transaction->type;
                event(new TransactionPaymentAdded($tp, $inputs));
              
          }
          
               
               
           }elseif($job_sheet->pay_types == 'insurance') {
               
               
               
                                
          $transaction =  Transaction::create([
                'business_id' => $business_id,
                'location_id' => $job_sheet->location_id,
                'type' => 'job_card',
                'status' => 'final',
                'payment_status' => 'due',
                'contact_id' => $job_sheet->contact_id,
                'transaction_date' => $job_sheet->created_at,
                'invoice_no' => $job_sheet->job_sheet_no,
                'total_before_tax' => $job_sheet->insurance_cost ,
                'final_total' => $job_sheet->insurance_cost ,
                'garage_total_cash' => $job_sheet->insurance_cost ,
                'garage_cash' => $job_sheet->insurance_cost ,
                'created_by' => $job_sheet->created_by,
                'garage_job_card_id' => $job_sheet->id,
                'garage_is_insurance' => 1,
                
                ]);
            
               
               
               
           }elseif($job_sheet->pay_types == 'both') {
               
               
               
                $transaction =  Transaction::create([
                'business_id' => $business_id,
                'location_id' => $job_sheet->location_id,
                'type' => 'job_card',
                'status' => 'final',
                'payment_status' => 'due',
                'contact_id' => $job_sheet->contact_id,
                'transaction_date' => $job_sheet->created_at,
                'invoice_no' => $job_sheet->job_sheet_no,
                'total_before_tax' => $job_sheet->cash_cost ,
                'final_total' => $job_sheet->cash_cost ,
                'garage_total_cash' => $job_sheet->cash_cost ,
                'garage_cash' => $job_sheet->cash_cost ,
                'created_by' => $job_sheet->created_by,
                'garage_job_card_id' => $job_sheet->id,
                'garage_is_cash' => 1,
                
                
                ]);
            
        
          
          
          if(!empty($job_sheet->deposit)){
              
              
                $inputs['paid_on'] = $transaction->transaction_date;
                $inputs['transaction_id'] = $transaction->id;
                $inputs['amount'] = $this->transactionUtil->num_uf($job_sheet->deposit);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['papers'] = 0;
                $inputs['payment_for'] = $transaction->contact_id;

            
                 $inputs['method'] = 'cash';

              

              
              $prefix_type = 'sell_payment';
             

               

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
              
                 $tp = TransactionPayment::create($inputs);
                
                //update payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                $inputs['transaction_type'] = $transaction->type;
                event(new TransactionPaymentAdded($tp, $inputs));
              
          }
               
               
               
         $transaction_ins =  Transaction::create([
                'business_id' => $business_id,
                'location_id' => $job_sheet->location_id,
                'type' => 'job_card',
                'status' => 'final',
                'payment_status' => 'due',
                'contact_id' => $job_sheet->contact_id,
                'transaction_date' => $job_sheet->created_at,
                'invoice_no' => $job_sheet->job_sheet_no,
                'total_before_tax' => $job_sheet->insurance_cost ,
                'final_total' => $job_sheet->insurance_cost ,
                'garage_total_cash' => $job_sheet->insurance_cost ,
                'garage_cash' => $job_sheet->insurance_cost ,
                'created_by' => $job_sheet->created_by,
                'garage_job_card_id' => $job_sheet->id,
                'garage_is_insurance' => 1,
                
                ]);
            
               
               
               
           }
  
          
             activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => '', 'updated_status' => '' ])
            ->log('job card created');
            
            DB::commit();

          if (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_add_parts') {
                return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@addParts', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
            } elseif (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_upload_docs') {
                return redirect()
                    ->action('\Modules\Garage\Http\Controllers\JobSheetController@getUploadDocs', [$job_sheet->id])
                    ->with('status', ['success' => true, 'msg' => __("lang_v1.success")]);
            }
 
            return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
    }


  public static function uploadFile($file)
    {
        $file_name = null;
        if ($file->getSize() <= config('constants.document_size_limit')) {
            $new_file_name = time() . '_' . mt_rand() . '_' . $file->getClientOriginalName();
            
           $resize_image = Image::make($file)->resize(800,800);
           $path = 'uploads/job_card/' . $new_file_name;
            if ($resize_image->save($path)) {
              $file_name = $new_file_name;
            }
         /*    if ($file->storeAs('/media', $new_file_name)) {
                $file_name = $new_file_name;
            }*/
        }

        return $file_name;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && (auth()->user()->can('garage.view') )) || (auth()->user()->can('garage.view')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->repair_settings, true);

        $activities = Activity::forSubject($job_sheet)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();
        
        return view('garage::job_sheet.show')
            ->with(compact('job_sheet', 'repair_settings', 'parts', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.update')) || (auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $job_sheet = JobCard::where('business_id', $business_id)
                    ->findOrFail($id);

        $repair_statuses = GarageStatus::getRepairSatuses($business_id);
        $brands = CarBrand::forDropdown($business_id, false, true);
        $devices = Company::forDropdown($business_id);
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
      

        return view('garage::job_sheet.edit')
            ->with(compact('job_sheet', 'repair_statuses',  'brands', 'devices', 'default_status', 'technecians', 'types', 'customer_groups', 'repair_settings'));
    }


 public function create_estimation($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.update')) || (auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $job_sheet = JobCard::where('business_id', $business_id)
                    ->findOrFail($id);

        $repair_statuses = GarageStatus::getRepairSatuses($business_id);
        $brands = CarBrand::forDropdown($business_id, false, true);
        $devices = Company::forDropdown($business_id);
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
      

        return view('garage::estimation.create')
            ->with(compact('job_sheet', 'repair_statuses',  'brands', 'devices', 'default_status', 'technecians', 'types', 'customer_groups', 'repair_settings'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.update')) || (auth()->user()->can('garage.update')) )) {
            abort(403, 'Unauthorized action.');
        }

        try {
               $input = $request->only('contact_id', 'car_status', 'serial_no','notes', 'status_id', 'completed_on', 'date_in', 'amount','estimation', 'deposit', 'repair_days',  'insurance_company_id', 'type', 'parts_desc', 'pay_types', 'service_staff', 'location_id', 'car_brand', 'car_plate', 'care_model',  'cash_desc',  'insurance_desc',  'cash_cost',  'insurance_cost', 'comment_by_ss', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5');
      ini_set('max_execution_time', 20000);
        ini_set('memory_limit', '512M');
            ini_set('post_max_size', '50M');
                 ini_set('max_input_vars', 2000);
        ini_set('max_input_time', 2000);

            
            if($input['pay_types'] == 'cash' ){
                
           if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = 0;
               
            } 
            
            $input['insurance_company_id'] = null;
            if (!empty($input['cash_cost'])) {
                $input['cash_cost'] = $this->commonUtil->num_uf($input['cash_cost']);
            }
    if (!empty($input['deposit'])) {
                $input['deposit'] = $this->commonUtil->num_uf($input['deposit']);
            }
                
            }elseif($input['pay_types'] == 'excess'){
                
             if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = $this->commonUtil->num_uf($input['insurance_cost']);
            }
            if (!empty($input['cash_cost'])) {
                $input['cash_cost'] = 0;
            }
  
    if (!empty($input['deposit'])) {
                $input['deposit'] = 0;
            }
                   $input['pay_types'] = 'insurance';
            }else{
                
                
            if (!empty($input['insurance_cost'])) {
                $input['insurance_cost'] = $this->commonUtil->num_uf($input['insurance_cost']);
            }
            if (!empty($input['cash_cost'])) {
                $input['cash_cost'] = $this->commonUtil->num_uf($input['cash_cost']);
            }

           if (!empty($input['deposit'])) {
                $input['deposit'] = $this->commonUtil->num_uf($input['deposit']);
                
                
            }
            }


            if (!empty($input['completed_on'])) {
                $input['completed_on'] = $this->commonUtil->uf_date($input['completed_on'], true);
            } 
            
            if (!empty($input['date_in'])) {
                $input['date_in'] = $this->commonUtil->uf_date($input['date_in'], true);
            }

            
                $image_64 = $request->car_diagram; //your base64 encoded data
                
                  $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
                
                  $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
                
                // find substring fro replace here eg: data:image/png;base64,
                
                 $image = str_replace($replace, '', $image_64); 
                
                 $image = str_replace(' ', '+', $image); 
                
                 $imageName = Str::random(10).'.'.$extension;
                
                $image = base64_decode($image) ;
                
               // dd($image);
             //    \Storage::disk('public')->put($imageName,$image );
             
             file_put_contents('uploads/job_card/'.$imageName, $image);
             
             
             
          //   $image->storeAs('uploads/job_card', $imageName);
                 
            $input['car_marks'] = $imageName ;
            
          /*  if(!empty($request->police_report)){
                
                $police_report = time() . '_' . $request->police_report->getClientOriginalName();
                if ($request->police_report->storeAs('job_card/', $police_report)) {
                    $input['police_report']  = $police_report;
                }  
            }
            
              if(!empty($request->id_photo)){
                
                  $id_photo = time() . '_' . $request->id_photo->getClientOriginalName();
                if ($request->id_photo->storeAs('job_card/', $id_photo)) {
                    $input['id_photo']  = $id_photo;
                }  
            }
          
                
                  if(!empty($request->d_license)){
                
                 $d_license = time() . '_' . $request->d_license->getClientOriginalName();
                if ($request->d_license->storeAs('job_card/', $d_license)) {
                    $input['d_license']  = $d_license;
                }
            }
              
                
                  if(!empty($request->v_license)){
                
                  $v_license = time() . '_' . $request->v_license->getClientOriginalName();
                if ($request->v_license->storeAs('job_card/', $v_license)) {
                    $input['v_license']  = $v_license;
                }
            
            }
           */
            
                    if ($request->hasFile('police_report')) {
            $files = $request->file('police_report');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['police_report'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
            
              if ($request->hasFile('id_photo')) {
            $files = $request->file('id_photo');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['id_photo'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
              if ($request->hasFile('d_license')) {
            $files = $request->file('d_license');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['d_license'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
              if ($request->hasFile('v_license')) {
            $files = $request->file('v_license');
            $uploaded_files = [];
                 
            //If multiple files present
            if (is_array($files)) {
                foreach ($files as $file) {
                    $uploaded_file = $this->uploadFile($file);

                    if (!empty($uploaded_file)) {
                        $uploaded_files[] = $uploaded_file;
                   
                        
                    }
                }
                
                
              $input['v_license'] = implode(',', str_replace(',',' ',$uploaded_files));  
                
            } 
        
        }
            
            
            
            DB::beginTransaction();

            $job_sheet = JobCard::where('business_id', $business_id)
                            ->findOrFail($id);
                            
                            
               
        if (!empty($input['estimation'])) {
                $input['estimation'] = 1;
              
                if(empty($job_sheet->serial_no)){
                    
                      $ref_count = $this->commonUtil->setAndGetReferenceCount('estimation', $business_id);
            $business = Business::find($business_id);
            $repair_settings = json_decode($business->garage_settings, true);

            $job_sheet_prefix = '';
            if (isset($repair_settings['job_sheet_prefix'])) {
                $job_sheet_prefix = $repair_settings['job_sheet_prefix'];
            }
            
            
              $input['serial_no'] = $this->commonUtil->generateReferenceNumber('estimation', $ref_count, null, $job_sheet_prefix);
          
                }
        
        
           if(empty($job_sheet->estimation_date)){
                    
                     $input['estimation_date'] = \Carbon::now() ;  
                }
             
               
            }else{
                
                  $input['estimation'] = 0;
                
            } 
            
            
                            
            $job_sheet->update($input);





     
           if($job_sheet->pay_types == 'cash') {
               
               
            
            $transaction =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_cash',1)->update([
              
               'total_before_tax' => $job_sheet->cash_cost ,
                'final_total' => $job_sheet->cash_cost ,
                'garage_total_cash' => $job_sheet->cash_cost ,
                'garage_cash' => $job_sheet->cash_cost ,
            
                ]);
            
     
          
          
          if(!empty($job_sheet->deposit)){
              
              
                $transaction =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_cash',1)->first();
              
                $inputs['amount'] = $this->transactionUtil->num_uf($job_sheet->deposit);
          
             
                 $tp = TransactionPayment::where('transaction_id',$transaction->id)->first();
                
if($tp){
    
    $tp->update($inputs);
    
    
}else{
    
    
     
                $inputs['paid_on'] = $transaction->transaction_date;
                $inputs['transaction_id'] = $transaction->id;
             
                $inputs['created_by'] = auth()->user()->id;
                $inputs['papers'] = 0;
                $inputs['payment_for'] = $transaction->contact_id;

            
                 $inputs['method'] = 'cash';

              

              
              $prefix_type = 'sell_payment';
             

               

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
              
                 $tp = TransactionPayment::create($inputs);
    
    
    
    
}


//update payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
       
              
          }
             
               
               
             
               
               
           }elseif($job_sheet->pay_types == 'insurance') {
               
               
        $transaction =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_insurance',1)->update([
              
               'total_before_tax' => $job_sheet->insurance_cost ,
                'final_total' => $job_sheet->insurance_cost ,
                'garage_total_cash' => $job_sheet->insurance_cost ,
                'garage_cash' => $job_sheet->insurance_cost ,
            
                ]);
                                
       
            
               
               
               
           }elseif($job_sheet->pay_types == 'both') {
               
                        
            $transaction =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_cash',1)->update([
              
               'total_before_tax' => $job_sheet->cash_cost ,
                'final_total' => $job_sheet->cash_cost ,
                'garage_total_cash' => $job_sheet->cash_cost ,
                'garage_cash' => $job_sheet->cash_cost ,
            
                ]);
            
     
          
          
          if(!empty($job_sheet->deposit)){
              
              
                $transaction =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_cash',1)->first();
              
                $inputs['amount'] = $this->transactionUtil->num_uf($job_sheet->deposit);
          
             
                 $tp = TransactionPayment::where('transaction_id',$transaction->id)->first();
                
if($tp){
    
    $tp->update($inputs);
    
    
}else{
    
    
     
                $inputs['paid_on'] = $transaction->transaction_date;
                $inputs['transaction_id'] = $transaction->id;
             
                $inputs['created_by'] = auth()->user()->id;
                $inputs['papers'] = 0;
                $inputs['payment_for'] = $transaction->contact_id;

            
                 $inputs['method'] = 'cash';

              

              
              $prefix_type = 'sell_payment';
             

               

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
              
                 $tp = TransactionPayment::create($inputs);
    
    
    
    
}


//update payment status
                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
       
              
          }
             
               
               
               
               $transaction_ins =  Transaction::where('garage_job_card_id',$job_sheet->id)->where('garage_is_insurance',1)->update([
              
               'total_before_tax' => $job_sheet->insurance_cost ,
                'final_total' => $job_sheet->insurance_cost ,
                'garage_total_cash' => $job_sheet->insurance_cost ,
                'garage_cash' => $job_sheet->insurance_cost ,
            
                ]);
                                
        
             
            
               
               
               
           }
  

          
          
          

            //upload media
            Media::uploadMedia($business_id, $job_sheet, $request, 'images');
            
            if (!empty($request->input('send_notification')) && in_array('sms', $request->input('send_notification'))) {
                $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                            ->find($job_sheet->status_id);
                if (!empty($status->sms_template)) $this->repairUtil->sendJobSheetUpdateSmsNotification($status->sms_template, $job_sheet); 
            }
            
            if (!empty($request->input('send_notification')) && in_array('email', $request->input('send_notification'))) {
                $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })
                            ->find($job_sheet->status_id);
                $notification = [
                        'subject' => $status->email_subject,
                        'body' => $status->email_body
                    ];
                if (!empty($status->email_subject) && !empty($status->email_body)) $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
            }

            DB::commit();

              activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => '', 'updated_status' => '' ])
            ->log('job card updated');
              if (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_send') {
                
              
                return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@SendEmail', [$job_sheet->id])
              ;
            } elseif (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_add_parts') {
                return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@addParts', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
            } elseif (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_upload_docs') {
                return redirect()
                    ->action('\Modules\Garage\Http\Controllers\JobSheetController@getUploadDocs', [$job_sheet->id])
                    ->with('status', ['success' => true, 'msg' => __("lang_v1.success")]);
            }

            return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.delete')) || (auth()->user()->can('garage.delete')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $job_sheet = JobCard::where('business_id', $business_id)
                    ->findOrFail($id);

                $job_sheet->delete();
                $job_sheet->invoices()->delete();
                $job_sheet->media()->delete();
                
                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (\Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Show the form for editing the status
     * @param int $id
     * @return Response
     */
    public function editStatus($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $job_sheet = JobCard::where('business_id', $business_id)->with(['status'])->findOrFail($id);

            $status_dropdown = GarageStatus::forDropdown($business_id, true,$job_sheet);
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();
            return view('garage::job_sheet.partials.edit_status')
                ->with(compact('job_sheet', 'status_dropdown', 'status_template_tags'));
        }
    }

    private function updateJobsheetStatus($input, $jobsheet_id)
    {
        $job_sheet = JobCard::where('business_id', $input['business_id'])->findOrFail($jobsheet_id);
        $job_sheet->status_id = $input['status_id'];
        $job_sheet->save();

        $status = GarageStatus::findOrFail($input['status_id']);

        //send job sheet updates
        if (!empty($input['send_sms'])) {
            $sms_body = $input['sms_body'];
            $response = $this->repairUtil->sendJobSheetUpdateSmsNotification($sms_body, $job_sheet);
        }

        if (!empty($input['send_email'])) {
                $subject = $input['email_subject'];
                $body = $input['email_body'];
                $notification = [
                    'subject' => $subject,
                    'body' => $body
                ];
            if (!empty($subject) && !empty($body)) $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
        }

        activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => $input['update_note'], 'updated_status' => $status->name  ])
            ->log('status_changed');
    }

    public function updateStatus(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                $input = $request->only([
                    'status_id',
                    'update_note'
                ]);

                $input['business_id'] = $business_id;

                if (!empty($request->input('send_sms'))) {
                    $input['send_sms'] = true;
                    $input['sms_body'] = $request->input('sms_body');
                }
                if (!empty($request->input('send_email'))) {
                    $input['send_email'] = true;
                    $input['email_body'] = $request->input('email_body');
                    $input['email_subject'] = $request->input('email_subject');
                }
                $status_id = $request->input('status_id');

                $status = GarageStatus::find($status_id);

                if ($status->is_completed_status == 1) {
                    $input['job_sheet_id'] = $id;
                    $request->session()->put('repair_status_update_data', $input);
                    return $output = ['success' => true];
                }

                $this->updateJobsheetStatus($input, $id);

                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function deleteJobSheetImage(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ( auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                Media::deleteMedia($business_id, $id);
                
                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (\Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function addParts($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $status_update_data = request()->session()->get('repair_status_update_data');

        $job_sheet = JobCard::where('business_id', $business_id)->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $status_dropdown = GarageStatus::forDropdown($business_id, true);
        $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();

        return view('garage::job_sheet.add_parts')
            ->with(compact('job_sheet', 'parts', 'status_update_data', 'status_dropdown', 'status_template_tags'));
    }

    public function saveParts(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $parts = $request->input('parts');
            $job_sheet = JobCard::where('business_id', $business_id)->findOrFail($id);
            $job_sheet->parts = !empty($parts) ? $parts : null;
            $job_sheet->save();

            if (!empty($request->session()->get('repair_status_update_data')) && !empty($request->input('status_id'))) {
                $input = $request->only([
                    'status_id',
                    'update_note'
                ]);

                $input['business_id'] = $business_id;

                if (!empty($request->input('send_sms'))) {
                    $input['send_sms'] = true;
                    $input['sms_body'] = $request->input('sms_body');
                }
                if (!empty($request->input('send_email'))) {
                    $input['send_email'] = true;
                    $input['email_body'] = $request->input('email_body');
                    $input['email_subject'] = $request->input('email_subject');
                }

                $this->updateJobsheetStatus($input, $job_sheet->id);

                $request->session()->forget('repair_status_update_data');
            }

            $output = ['success' => true,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }

        return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
    }

    public function jobsheetPartRow(Request $request)
    {
        if (request()->ajax()) {
            $variation_id = $request->input('variation_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id);

            $variation_name = $product->product_name . ' - ' . $product->sub_sku;
            $variation_id = $product->variation_id;
            $quantity = 1;
            $unit = $product->unit;
            
            return view('garage::job_sheet.partials.job_sheet_part_row')
            ->with(compact('variation_name', 'variation_id', 'quantity', 'unit'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function print($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('garage.view')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
        
        $html = view('garage::job_sheet.print_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('garage::lang.job_sheet') . ' | ' . $job_sheet->job_sheet_no);
        $mpdf->WriteHTML($html);
        $mpdf->Output('job_sheet.pdf', 'I');

        return view('garage::job_sheet.print_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'));
    }

    public function print_estimation($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('garage.view')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
        
        $html = view('garage::job_sheet.print_estimation_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('garage::lang.job_sheet') . ' | ' . $job_sheet->serial_no);
        $mpdf->WriteHTML($html);   
        $mpdf->Output('job_sheet.pdf', 'I');
        
   
             

        return view('garage::job_sheet.print_estimation_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'));
    }


    public function getUploadDocs($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $job_sheet = JobCard::with(['media'])
                        ->where('business_id', $business_id)
                        ->findOrFail($id);

        return view('garage::job_sheet.upload_doc', compact('job_sheet'));
    }

    public function postUploadDocs(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $images = json_decode($request->input('images'), true);

            $job_sheet = JobCard::where('business_id', $business_id)
                        ->findOrFail($request->input('job_sheet_id'));

            if (!empty($images) && !empty($job_sheet)) {

                Media::attachMediaToModel($job_sheet, $business_id, $images);
            }

            $output = ['success' => true,
                'msg' => __("lang_v1.success")
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
            ->with('status', ['success' => true,
                'msg' => __("lang_v1.success")]);
    }
    
       // get customer data 
    
    public function getCustomer(Request $request) {
        
      

         $id = $request->id;
     
       
       

        $business_id = request()->session()->get('user.business_id');
     
        
       $customer =  Contact::where('id','=',$id)->first();
     
     $orders = JobCard::where('business_id', $business_id)->where('contact_id',$id)->orderby('id','DESC')->get();                  
              
        
   
   
     $dataa = view('garage::job_sheet.partials.customer_data',compact('customer','orders'))->render();
            
            
              
      return response()->json(['options'=>$dataa]);


    } //end customer method
    
    
    
    public function editCarStatus($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $job_sheet = JobCard::where('business_id', $business_id)->with(['status'])->findOrFail($id);

            $status_dropdown = GarageStatus::forDropdown($business_id, true);
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();
            return view('garage::job_sheet.partials.edit_car_status')
                ->with(compact('job_sheet', 'status_dropdown', 'status_template_tags'));
        }
    }
    
    public function updateCarStatus(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
              
                $input = $request->only([
                    'car_status',
                    'update_date',
                    'update_note'
                ]);


                $job_sheet = JobCard::where('business_id', $business_id)->findOrFail($id);
               $job_sheet->car_status = $input['car_status'];
               $job_sheet->date_in = $this->commonUtil->uf_date($input['update_date'] , true);  ;
               $job_sheet->update();

                

         activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => $input['update_note'].' at '.$input['update_date'] , 'updated_status' => $input['car_status']  ])
            ->log('car status changed to '. $input['car_status']);

                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    } 
    
   
   

      public function addphoto($id) {
        
       
        
        $td = JobCard::where('id',$id)->first();
          
        return view('garage::job_sheet.partials.addphoto')
                ->with(compact('td'));
        
    }
   
    public function savephoto(Request $request) {
        
   
          
     
     try {   
        $td = JobCard::where('id',$request->jobcard_id)->first();
       
  
          if(!empty($request->job_card_photo)) {
              
              
            $police_report = time() . '_' . $request->job_card_photo->getClientOriginalName();
                if ($request->job_card_photo->storeAs('job_card/', $police_report)) {
                    
                }  
           
        $td->job_card_photo  = $police_report;  
              
              
          }   
          
            if(!empty($request->estimation_photo)) {
              
              
            $police_report = time() . '_' . $request->estimation_photo->getClientOriginalName();
                if ($request->estimation_photo->storeAs('job_card/', $police_report)) {
                    
                }  
           
        $td->estimation_photo  = $police_report;  
              
              
          }   
          
       if(!empty($request->lpo_photo)) {
              
              
            $police_report = time() . '_' . $request->lpo_photo->getClientOriginalName();
                if ($request->lpo_photo->storeAs('job_card/', $police_report)) {
                    
                }  
           
        $td->lpo_photo  = $police_report;  
              
              
          }   
          
           if(!empty($request->receipt_photo)) {
              
              
            $police_report = time() . '_' . $request->receipt_photo->getClientOriginalName();
                if ($request->receipt_photo->storeAs('job_card/', $police_report)) {
                    
                }  
           
        $td->receipt_photo  = $police_report;  
              
              
          }   
              if(!empty($request->invoice_photo)) {
              
              
            $police_report = time() . '_' . $request->invoice_photo->getClientOriginalName();
                if ($request->invoice_photo->storeAs('job_card/', $police_report)) {
                    
                }  
           
        $td->invoice_photo  = $police_report;  
              
              
          }   
          
          
          
          
                
        $td->save();  
       
        
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
                ->with('status', $output);
        
    
    }
    
   
   
   
    public function SendEmail($id) {
   
    try{   
        
          $business_id = request()->session()->get('user.business_id');
          
        $job_sheet = JobCard::where('id',$id)->first();
        
          $status = Company::where('business_id', $business_id)
                            ->find($job_sheet->insurance_company_id);
                              
           $repair_settings = $this->repairUtil->getRepairSettings($business_id);                   
                            
                   $email_subject = !empty($repair_settings['email_subject']) ?  $repair_settings['email_subject'] : '' ;         
                   $email_body = !empty($repair_settings['email_body']) ?  $repair_settings['email_body'] : '' ;   
                   
           $notification = [
                        'subject' => $email_subject,
                        'body' => $email_body
                    ];
      $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);                
    $html = view('garage::job_sheet.print_estimation_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('garage::lang.job_sheet') . ' | ' . $job_sheet->serial_no);
        $mpdf->WriteHTML($html);   
        
        $imageName = str_replace("/", "-", $job_sheet->serial_no).'.pdf';
                
               
                
               // dd($image);
             //    \Storage::disk('public')->put($imageName,$image );
             
        $mpdf->Output('uploads/job_card/'.$imageName, 'F');
     
        
           $job_sheet->estimation_pdf = $imageName;
            $job_sheet->status_id = 3 ;

           $job_sheet->update() ;
                    
                    
        if (!empty($email_subject) && !empty($email_body)) $this->repairUtil->sendJobSheetUpdateEmailNotificationcompany($notification, $job_sheet); 
        
        
          unlink(public_path('uploads/job_card/'.$imageName));
            
       activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => '', 'updated_status' => 'LPO_pending' ])
            ->log('status changed to LPO_pending');
         
         
          
         return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
                    
          } catch (Exception $e) {
              
          return redirect()
                ->action('\Modules\Garage\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => false,
                    'msg' => __("messages.something_went_wrong")]);
               
            }            
                    
        
    }
    
    
        public function editRepairStatuses($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || (auth()->user()->can('garage.update') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $job_sheet = JobCard::where('business_id', $business_id)->with(['status'])->findOrFail($id);

            $status_dropdown = GarageStatus::repairforDropdown($business_id, true);
            
            
            
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();
            return view('garage::job_sheet.partials.edit_repair_status')
                ->with(compact('job_sheet', 'status_dropdown', 'status_template_tags'));
        }
    }

    public function updateRepairStatuses(Request $request ,  $jobsheet_id)
    {
        
       if ($request->ajax()) {
            try {
                 
           $business_id = request()->session()->get('user.business_id');
         $input = $request->all();
        $job_sheet = JobCard::where('business_id', $business_id)->findOrFail($jobsheet_id);
        $job_sheet->repair_status = $input['status_id'];
        $job_sheet->save();

        $status = GarageStatus::findOrFail($input['status_id']);

    

        activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => $input['update_note'], 'updated_status' => $status->name  ])
             ->log('repair status changed to '. $status->name);
             
        
                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }     
             
    }



  public function printtest($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
        
  
        return view('garage::job_sheet.print_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'));
    }

    
    
  public function car_work($id)
    {   
        
          $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
  
  /*        
          $html = view('garage::invoices.insurance')->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('garage::lang.job_sheet') . ' | ' . $job_sheet->serial_no);
        $mpdf->WriteHTML($html);   
        $mpdf->Output('job_sheet.pdf', 'I');*/
  
        return view('garage::invoices.car_work')->with(compact('job_sheet', 'repair_settings', 'parts'));
    }
    
      public function cash($id)
    {   
        
          $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
  
  /*        
          $html = view('garage::invoices.insurance')->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('garage::lang.job_sheet') . ' | ' . $job_sheet->serial_no);
        $mpdf->WriteHTML($html);   
        $mpdf->Output('job_sheet.pdf', 'I');*/
  
        return view('garage::invoices.cash')->with(compact('job_sheet', 'repair_settings', 'parts'));
    }
    
 public function insurance($id)
    {   
        
     

        if (!(auth()->user()->can('superadmin') ||  (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('garage.update')))) {
            abort(403, 'Unauthorized action.');
        }

     
          if (request()->ajax()) {
              
              
            return $output;
        }
        
            try {
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];

               $business_id = request()->session()->get('user.business_id');
            
             
                 $query = JobCard::with('customer',
                        'customer.business', 'technician',
                        'company', 'businessLocation', 'invoice', 'status', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin get only assgined/created_by job sheet
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('garage_job_cards.service_staff', $user_id)
                    ->orWhere('garage_job_cards.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->garage_settings, true);
        

             if (empty($job_sheet)) {
                    return $output;
                }
           
            //  $receipt = view('garage::invoices.insurance')->with(compact('job_sheet', 'repair_settings', 'parts'))->render();

                
                
                
                    $output = ['success' => 1, 'receipt' => $receipt];
                    
                  
             
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];
            }

        
        return view('garage::invoices.insurance')->with(compact('job_sheet', 'repair_settings', 'parts'));
  
     
    }

    
}
