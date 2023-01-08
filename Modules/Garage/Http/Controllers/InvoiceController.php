<?php


namespace Modules\Garage\Http\Controllers;

use App\Http\traits\MylerzTrait;
use App\Http\traits\AramexTrait;
use App\Http\traits\BostaTrait;
use App\Http\traits\FastloTrait;
use App\Http\traits\AbsTrait;
use App\Http\traits\FedexTrait;
use App\Http\traits\PicksTrait;
use App\Account;
use App\VariationLocationDetails;
use App\Business;
use App\TransactionPayment;
use App\BusinessLocation;
use App\Models\Shipment;
use App\Models\ShippingType;
use App\Models\CityZone;
use App\Models\ShipmentPrice;
use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use App\Contact;
use App\Address;
use App\CustomerGroup;
use App\NotificationOrder;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TypesOfService;
use App\User;
use App\AssignUser;
use App\CallLog;
use App\AllLog;
use App\StatusLog;
use Carbon\Carbon;
use App\Arabian\OrderArabian;
use App\Arabian\TrackArabian;
use App\Variation;
use App\Product;
use App\CarrierAccount;
use App\Cottonesta\OrderCottonesta;
use App\Cottonesta\TrackCottonesta;
use App\Coupon;
use App\TraficResource;
use App\Campaign;
use App\RefNo;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Routing\Controller;
use Modules\Garage\Entities\JobSheet;
use Modules\Garage\Entities\Company;
use Modules\Garage\Entities\JobCard;
use Modules\Garage\Entities\GarageStatus;
use App\Imports;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Warranty;
use Modules\Garage\Utils\GarageUtil;
use App\Notifications\ArabianEmailNotification;
use Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use App\Exports\RefsExport;
use App\Exports\ShipInfoExport;
use App\Imports\OrdersImport;
use App\Imports\RefImport;


class InvoiceController extends Controller
{
    /**
     * All Utils instance.
     *
     */
     

    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $repairUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil,GarageUtil $repairUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->repairUtil = $repairUtil;

        $this->dummyPaymentLine = ['method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];

        $this->shipping_status_colors = [
            'pending' => 'bg-yellow',
            'pickup' => 'bg-info',
            'storage' => 'bg-navy',
            'postponement' => 'bg-blue',
            'Ready_To_Pickup' => 'bg-light-green',
            'delivered' => 'bg-green',
            'need_action' => 'bg-navy',
            'cancelled' => 'bg-red',
            'Returned' => 'bg-red'
        ]; 
        
        $this->order_status_colors = [
            'pending' => 'bg-yellow',
            'no answer' => 'bg-info',
            'follow Up' => 'bg-navy',
            'order done' => 'bg-green',
            'canceled' => 'bg-red',
            'on_returning' => 'bg-red'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
    public function index()
    {
        
       

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');
        $order_statuses = $this->transactionUtil->order_statuses();
        
        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types();
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statusess();
            
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                  ->leftJoin(
                    'garage_job_cards',
                    'transactions.garage_job_card_id',
                    '=',
                    'garage_job_cards.id'
                )
           
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as s', 'garage_job_cards.service_staff', '=', 's.id')
         
              ->leftJoin(
                        'garage_car_brands AS b',
                        'garage_job_cards.car_brand',
                        '=',
                        'b.id'
                    )
            
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
               
             
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
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'job_card')
                ->where('transactions.status', 'final')
            
                 ->where('transactions.garage_is_cash', 1)
           
                ->select(
                    'transactions.id',
                     'rs.view_name as status', 
                    'rrs.view_name as re_status', 
                    'garage_job_cards.id as garage_id', 
                    'garage_job_cards.car_status', 
                    'garage_job_cards.job_sheet_no', 
                    'garage_job_cards.pay_types', 
                    'garage_job_cards.completed_on', 
                    'garage_job_cards.date_in', 
                    'garage_job_cards.care_model', 
                    'garage_job_cards.created_at as created_at',
                    'b.name as brand', 
                    'rs.color as status_color',
                    'rrs.color as re_status_color',
                    
                    'transactions.transaction_date',
                
                    'transactions.invoice_no',
                  
                    'contacts.name',
                    'contacts.mobile',
           
                    'transactions.payment_status',
                    'transactions.final_total',
              
                    'transactions.tax_amount',
                
                    'transactions.total_before_tax',
              
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw("CONCAT(COALESCE(s.surname, ''),' ',COALESCE(s.first_name, ''),' ',COALESCE(s.last_name,'')) as technecian"),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id) as total_paid'),
                    'bl.name as location'
             
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

          /*  if (!auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }*/

            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

         
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
   
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

   
       
            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

         

            $sells->groupBy('transactions.id');

         
            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

           

            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
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
                                <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@show', ['id' => $row->garage_id]) . '" class="cursor-pointer"><i class="fa fa-eye"></i> '.__("messages.view").'
                                </a>
                                </li>';
                    }

                  

                    if (auth()->user()->can("job_sheet.edit")) {
                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@edit', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet"><i class="fa fa-edit"></i> '.__("messages.edit").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addParts', ['id' => $row->garage_id]) . '" class="cursor-pointer">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_parts").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@getUploadDocs', ['id' => $row->garage_id]) . '" class="cursor-pointer">
                                        <i class="fas fa-file-alt"></i>
                                        '.__("garage::lang.upload_docs").'
                                    </a>
                                </li>';
                    }

                    $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@print', ['id' => $row->garage_id]) . '" target="_blank"><i class="fa fa-print"></i> '.__("messages.print").'
                                    </a>
                                </li>';

                    if (auth()->user()->can("job_sheet.create") || auth()->user()->can("job_sheet.edit")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editStatus', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_status").'
                                    </a>
                                </li>';
                    }

                    if (auth()->user()->can("job_sheet.delete")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@destroy', ['id' => $row->garage_id]) . '"  id="delete_job_sheet" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>'; 
                                
                              
                    }
                            $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addphoto', ['id' => $row->garage_id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.jobcard_photo").'
                                    </a>
                                </li>';
                                
                       if($row->pay_types == "both" || $row->pay_types == "insurance"){        
                      $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\LpoController@addlpo', ['id' => $row->garage_id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_lpo").'
                                    </a>
                                </li>';
                        }
                         $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editRepairStatuses', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_repair_status").'
                                    </a>
                                </li>';   
                                
                                
                          
                           
                      $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@cash', ['id' => $row->garage_id]) . '" class="cursor-pointer print_insurance"  target="_blank">
                                        <i class="fa fa-print"></i>
                                        '.__("garage::lang.print_cash").'
                                    </a>
                                </li>';   
                                
                     
                                
                    $html .= '</ul>
                            </div>';
                    return $html;
                }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="display_currency total-tax" data-currency_symbol="true" data-orig-value="{{$tax_amount}}">{{$tax_amount}}</span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="{{$total_paid}}">{{$total_paid}}</span>'
                )
              
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                 ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->garage_id .'">' ;
                })
                
              
             
            
            
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                    }
                ) 
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid ;
                    $total_remaining_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';

                    
                    return $total_remaining_html;
                })
              
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                  
                    return $invoice_no;
                })
             
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = !empty($methods[0]) ? $payment_types[$methods[0]] : ''  ;
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';
                    
                    return $html;
                })
                
              /*  ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }, 'style' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return   $row->print1 == 1 ? 'background:#ffd9be ': 'none';
                        } else {
                            return '';
                        }
                    }])*/;

            $rawColumns = ['final_total', 'action', 'final_without_charge','shipping_charges','checksell', 'mass_delete','logs','total_items', 'total_paid','order_status', 'total_remaining','trafic_id','campaign_id', 'shipment_id', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due','ref_no','commission_agent'];
                
            return $datatable->rawColumns($rawColumns)
                      ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, false);
       $status_dropdown = GarageStatus::forDropdown($business_id);

        return view('garage::invoices.index')
        ->with(compact('business_locations', 'customers', 'sales_representative', 'status_dropdown'));
    }
    

    public function insurance()
    {
        
       

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');
        $order_statuses = $this->transactionUtil->order_statuses();
        
        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types();
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statusess();
            
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                  ->leftJoin(
                    'garage_job_cards',
                    'transactions.garage_job_card_id',
                    '=',
                    'garage_job_cards.id'
                )
           
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as s', 'garage_job_cards.service_staff', '=', 's.id')
         
              ->leftJoin(
                        'garage_car_brands AS b',
                        'garage_job_cards.car_brand',
                        '=',
                        'b.id'
                    )
            
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
               
             
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
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'job_card')
                ->where('transactions.status', 'final')
            
                 ->where('transactions.garage_is_insurance', 1)
           
                ->select(
                    'transactions.id',
                     'rs.view_name as status', 
                    'rrs.view_name as re_status', 
                    'garage_job_cards.id as garage_id', 
                    'garage_job_cards.car_status', 
                    'garage_job_cards.job_sheet_no', 
                    'garage_job_cards.pay_types', 
                    'garage_job_cards.completed_on', 
                    'garage_job_cards.date_in', 
                    'garage_job_cards.care_model', 
                    'garage_job_cards.created_at as created_at',
                    'b.name as brand', 
                    'rs.color as status_color',
                    'rrs.color as re_status_color',
                    
                    'transactions.transaction_date',
                
                    'transactions.invoice_no',
                  
                    'contacts.name',
                    'contacts.mobile',
           
                    'transactions.payment_status',
                    'transactions.final_total',
              
                    'transactions.tax_amount',
                
                    'transactions.total_before_tax',
              
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw("CONCAT(COALESCE(s.surname, ''),' ',COALESCE(s.first_name, ''),' ',COALESCE(s.last_name,'')) as technecian"),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id) as total_paid'),
                    'bl.name as location'
             
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

          /*  if (!auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }*/

            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

         
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
   
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

   
       
            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

         

            $sells->groupBy('transactions.id');

         
            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

           

            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
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
                                <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@show', ['id' => $row->garage_id]) . '" class="cursor-pointer"><i class="fa fa-eye"></i> '.__("messages.view").'
                                </a>
                                </li>';
                    }

                  

                    if (auth()->user()->can("job_sheet.edit")) {
                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@edit', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet"><i class="fa fa-edit"></i> '.__("messages.edit").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addParts', ['id' => $row->garage_id]) . '" class="cursor-pointer">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_parts").'
                                    </a>
                                </li>';

                        $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@getUploadDocs', ['id' => $row->garage_id]) . '" class="cursor-pointer">
                                        <i class="fas fa-file-alt"></i>
                                        '.__("garage::lang.upload_docs").'
                                    </a>
                                </li>';
                    }

                    $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@print', ['id' => $row->garage_id]) . '" target="_blank"><i class="fa fa-print"></i> '.__("messages.print").'
                                    </a>
                                </li>';

                    if (auth()->user()->can("job_sheet.create") || auth()->user()->can("job_sheet.edit")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editStatus', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_status").'
                                    </a>
                                </li>';
                    }

                    if (auth()->user()->can("job_sheet.delete")) {
                        $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@destroy', ['id' => $row->garage_id]) . '"  id="delete_job_sheet" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        '.__("messages.delete").'
                                    </a>
                                </li>'; 
                                
                              
                    }
                            $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@addphoto', ['id' => $row->garage_id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.jobcard_photo").'
                                    </a>
                                </li>';
                                
                       if($row->pay_types == "both" || $row->pay_types == "insurance"){        
                      $html .= '<li>
                                    <a data-container=".view_modal" data-href="' . action('\Modules\Garage\Http\Controllers\LpoController@addlpo', ['id' => $row->garage_id]) . '"   class="cursor-pointer btn-modal">
                                        <i class="fas fa-toolbox"></i>
                                        '.__("garage::lang.add_lpo").'
                                    </a>
                                </li>';
                        }
                         $html .= '<li>
                                    <a data-href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@editRepairStatuses', ['id' => $row->garage_id]) . '" class="cursor-pointer edit_job_sheet_status">
                                        <i class="fa fa-edit"></i>
                                        '.__("garage::lang.change_repair_status").'
                                    </a>
                                </li>';   
                                
                                
                          
                           
                      $html .= '<li>
                                    <a href="' . action('\Modules\Garage\Http\Controllers\JobSheetController@insurance', ['id' => $row->garage_id]) . '" class="cursor-pointer print_insurance"  target="_blank">
                                        <i class="fa fa-print"></i>
                                        '.__("garage::lang.print_insurance").'
                                    </a>
                                </li>';   
                                
                     
                                
                    $html .= '</ul>
                            </div>';
                    return $html;
                }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="display_currency total-tax" data-currency_symbol="true" data-orig-value="{{$tax_amount}}">{{$tax_amount}}</span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="{{$total_paid}}">{{$total_paid}}</span>'
                )
              
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                 ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->garage_id .'">' ;
                })
                
              
             
            
            
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                    }
                ) 
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid ;
                    $total_remaining_html = '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';

                    
                    return $total_remaining_html;
                })
              
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                  
                    return $invoice_no;
                })
             
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = !empty($methods[0]) ? $payment_types[$methods[0]] : ''  ;
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';
                    
                    return $html;
                })
                
              /*  ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }, 'style' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return   $row->print1 == 1 ? 'background:#ffd9be ': 'none';
                        } else {
                            return '';
                        }
                    }])*/;

            $rawColumns = ['final_total', 'action', 'final_without_charge','shipping_charges','checksell', 'mass_delete','logs','total_items', 'total_paid','order_status', 'total_remaining','trafic_id','campaign_id', 'shipment_id', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due','ref_no','commission_agent'];
                
            return $datatable->rawColumns($rawColumns)
                      ->make(true);
        }

   
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
