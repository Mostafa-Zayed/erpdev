<?php

namespace Modules\Garage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Garage\Utils\GarageUtil;
use App\Utils\BusinessUtil;
use Modules\Garage\Entities\JobSheet;
use Modules\Garage\Entities\Company;
use Modules\Garage\Entities\JobCard;
use Modules\Garage\Entities\GarageStatus;
use DB;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use App\Brands;
use App\BusinessLocation;
use App\Business;
use App\Contact;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * All Utils instance.
     *
     */
       protected $businessUtil;
    protected $repairUtil;
    protected $commonUtil;
    protected $moduleUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(  BusinessUtil $businessUtil,Util $commonUtil,ModuleUtil $moduleUtil,GarageUtil $repairUtil) {
        $this->businessUtil = $businessUtil;
        $this->repairUtil = $repairUtil;
           $this->commonUtil = $commonUtil;
                  $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {   
        $business_id = request()->session()->get('user.business_id');
        $job_sheets_by_status = $this->repairUtil->getRepairByStatus($business_id);
        $job_sheets_by_service_staff = $this->repairUtil->getRepairByServiceStaff($business_id);
      //  $trending_brand_chart = $this->repairUtil->getTrendingRepairBrands($business_id);
      //  $trending_devices_chart = $this->repairUtil->getTrendingDevices($business_id);
      //  $trending_dm_chart = $this->repairUtil->getTrendingDeviceModels($business_id);
        
        
        
         $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));
        
        $companies = Company::where('business_id',$business_id)->get();
         $business_locations = BusinessLocation::forDropdown($business_id, false);
        
        return view('garage::dashboard.index')
            ->with(compact('job_sheets_by_status', 'job_sheets_by_service_staff','date_filters','companies','business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
     
     
         public function getTotals()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            
            

            $total = $this->getallTotals($business_id, $start, $end,$location_id,$user_id);

         
         
            
            
            $output['total_cash'] = $total->where('insurance_company_id',null)->count();
            $output['all_total'] = $total->count();
            
               $companies = Company::where('business_id',$business_id)->get();
               
               foreach($companies as $company){
                   
               $output[$company->name] = $total->where('insurance_company_id',$company->id)->count();  
               }
            
            
            return $output;
        }
    }


    public function getallTotals($business_id, $start_date = null, $end_date = null, $location_id = null, $created_by = null)
    {
        $query = JobCard::where('business_id', $business_id);
                  
                   

        //Check for permitted locations of a user
  

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('created_at', '<=', $end_date);
        } 
        
        if (!empty($location_id)) {
            $query->where('location_id', '=', $location_id);
        }

     

        $sell_details = $query->get();
      
    
        

      //  $output['total_paids'] = $sell_details->sum('amount');
      
      
        
        

        return $sell_details;
    }
    
    
     public function getCompanyTotals($id = null)
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
               $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            
            

            $total = $this->getCompanyallTotals($business_id, $start, $end,$location_id,$user_id,$id);

         
         
            
            
           
            
               $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })->get();
               $output['all_total'] = $total->count();
               foreach($status as $company){
                if($company->name == 'car_repairing') {
                    
                    $output[$company->name] = $total->where('status_id','!=',8)->where('status_id','!=',7)->where('car_status','=','in')->count(); 
                }else{
                               $output[$company->name] = $total->where('status_id',$company->id)->count();     
                    
                }  

               
               
               }
            
            
            return $output;
        }
    }


    public function getCompanyallTotals($business_id, $start_date = null, $end_date = null, $location_id = null, $created_by = null, $id = null)
    {
        $query = JobCard::where('business_id', $business_id)->where('insurance_company_id',$id);
                  
                   

        //Check for permitted locations of a user
  

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('created_at', '<=', $end_date);
        }

      if (!empty($location_id)) {
            $query->where('location_id', '=', $location_id);
        }

        $sell_details = $query->get();
      
    
        

      //  $output['total_paids'] = $sell_details->sum('amount');
      
      
        
        

        return $sell_details;
    }
    
    
      public function getStatusTotal($id = null)
    {
         
        $business_id = request()->session()->get('user.business_id');
        $job_sheets_by_status = $this->repairUtil->getRepairByStatus($business_id);
        $job_sheets_by_service_staff = $this->repairUtil->getRepairByServiceStaff($business_id);
      //  $trending_brand_chart = $this->repairUtil->getTrendingRepairBrands($business_id);
      //  $trending_devices_chart = $this->repairUtil->getTrendingDevices($business_id);
      //  $trending_dm_chart = $this->repairUtil->getTrendingDeviceModels($business_id);
        
        
        
         $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));
        
        $company = Company::where('business_id',$business_id)->find($id);
        
        if($company){
            
            $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })->whereIn('type',['insurance','both'])->orderby('sort_order','asc')->get();  
            
        }else{
            
            
                $status = GarageStatus::where(function ($q)use($business_id) {
                    $q->where('business_id',$business_id)
                    ->orWhere('business_id', null);
                })->whereIn('type',['cash','both'])->orderby('sort_order','asc')->get();  
            
        }
      
         $business_locations = BusinessLocation::forDropdown($business_id, false);
        
        
        
        return view('garage::dashboard.company')
            ->with(compact('job_sheets_by_status', 'job_sheets_by_service_staff','date_filters','company','business_locations','status','id'));
    
    }
    
    
    
    public function create()
    {
        return view('garage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('garage::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('garage::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    } 
    
    
  
      public function getallTotal($status = 0 , $id = null)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create'))) || (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create')))) {
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
                    ->where('garage_job_cards.insurance_company_id', $id)
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
            if (!empty(request()->car_plate)) {
                $job_sheets->where('garage_job_cards.car_plate' , 'like', '%' . request()->car_plate .'%' );
            } 
            
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
            
            
            if (!empty($status)) {
                if($status == 13){
                    
                    $job_sheets->where('garage_job_cards.status_id','!=',8)->where('garage_job_cards.status_id','!=',7)->where('garage_job_cards.car_status','=','in');
                }else{
                    
                     $job_sheets->where('garage_job_cards.status_id', $status);
                }
               
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

                    if (auth()->user()->can("job_sheet.view_assigned") || auth()->user()->can("job_sheet.view_all") || auth()->user()->can("job_sheet.create")) {
                            $html .= '<li>
                                <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@show', ['id' => $row->id]) . '" class="cursor-pointer"><i class="fa fa-eye"></i> '.__("messages.view").'
                                </a>
                                </li>';
                    }

                  

                    if (auth()->user()->can("job_sheet.edit")) {
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

                    if (auth()->user()->can("job_sheet.create") || auth()->user()->can("job_sheet.edit")) {
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
                    $html .= '</ul>
                            </div>';
                    return $html;
                })  ->addColumn(
                    'actions',
                    '@can("job_sheet.update")
                        <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    @endcan
                    @if (auth()->user()->can("job_sheet.view_assigned") || auth()->user()->can("job_sheet.view_all") || auth()->user()->can("job_sheet.create")) 
                    <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
                    &nbsp;
                    @endif
                    @if($pay_types == "both" || $pay_types == "insurance")
                        <a href="{{action(\'\Modules\Garage\Http\Controllers\JobSheetController@create_estimation\', [$id])}}" class="btn btn-xs btn-primary "><i class="glyphicon glyphicon-list"></i> @lang("garage::lang.estimation")</a>
                      @endif 
                         @can("user.delete")  @endcan'
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

        return view('garage::dashboard.get_all_total')
            ->with(compact('business_locations', 'customers', 'status_dropdown', 'service_staffs', 'is_user_service_staff','id','status'));
    }
  
    
  
}
