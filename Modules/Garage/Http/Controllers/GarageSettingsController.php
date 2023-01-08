<?php

namespace Modules\Garage\Http\Controllers;

use App\Barcode;
use App\Brands;
use App\Business;
use App\Category;
use App\Utils\ModuleUtil;
use App\Variation;
use Illuminate\Http\Request;
use App\BusinessLocation;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Garage\Entities\GarageStatus;
use Modules\Garage\Utils\GarageUtil;
use Yajra\DataTables\Facades\DataTables;

class GarageSettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $repairUtil;
    protected $moduleUtil;


    /**
     * Constructor
     *
     * @param RepairUtil $repairUtil
     * @return void
     */
    public function __construct(GarageUtil $repairUtil, ModuleUtil $moduleUtil)
    {
        $this->repairUtil = $repairUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    
    {
        
      
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $barcode_settings = Barcode::where('business_id', $business_id)
                                ->orWhereNull('business_id')
                                ->pluck('name', 'id');

        $repair_settings = $this->repairUtil->getRepairSettings($business_id);

        $default_product_name = __('garage::lang.no_default_product_selected');
        if (!empty($repair_settings['default_product'])) {
            $default_product = Variation::where('id', $repair_settings['default_product'])
                        ->with(['product_variation', 'product'])
                        ->first();

            $default_product_name = $default_product->product->type == 'single' ? $default_product->product->name . ' - ' . $default_product->product->sku : $default_product->product->name . ' (' . $default_product->name . ') - ' . $default_product->sub_sku;
        }

        //barcode types
        $barcode_types = $this->moduleUtil->barcode_types();
        $repair_statuses = GarageStatus::getRepairSatuses($business_id);

        $brands = Brands::forDropdown($business_id);
        $devices = Category::forDropdown($business_id, 'device');
       // $module_category_data =  $this->moduleUtil->getTaxonomyData('device');
        $module_category_data =  [];
        
          $status_template_tags = $this->repairUtil->getGaragetatusTemplateTags();
        
        return view('garage::settings.index')
                ->with(compact('barcode_settings', 'repair_settings', 'default_product_name', 'barcode_types', 'repair_statuses', 'brands', 'devices', 'module_category_data', 'status_template_tags'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.settings')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['barcode_id', 'default_product', 'barcode_type', 'repair_tc_condition', 'repair_tc_condition_ar','email_body','email_subject', 'job_sheet_prefix', 'problem_reported_by_customer', 'product_condition', 'product_configuration', 'job_sheet_custom_field_1', 'job_sheet_custom_field_2', 'job_sheet_custom_field_3', 'job_sheet_custom_field_4', 'job_sheet_custom_field_5']);

            $default_status = $request->get('default_status');
            if (!empty($default_status) && is_numeric($default_status)) {
                $input['default_status'] = $default_status;
            } else {
                $input['default_status'] = '';
            }

            Business::where('id', $business_id)
                        ->update(['garage_settings' => json_encode($input)]);

            $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
    
    
    
     public function locations(){
         
                 if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $locations = BusinessLocation::where('business_locations.business_id', $business_id)
                ->leftjoin(
                    'invoice_schemes as ic',
                    'business_locations.invoice_scheme_id',
                    '=',
                    'ic.id'
                )
                ->leftjoin(
                    'invoice_layouts as il',
                    'business_locations.invoice_layout_id',
                    '=',
                    'il.id'
                )
                ->leftjoin(
                    'selling_price_groups as spg',
                    'business_locations.selling_price_group_id',
                    '=',
                    'spg.id'
                )
                ->select(['business_locations.name', 'location_id', 'landmark', 'city', 'zip_code', 'state',
                    'country', 'business_locations.id', 'spg.name as price_group', 'ic.name as invoice_scheme', 'il.name as invoice_layout', 'business_locations.is_active']);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $locations->whereIn('business_locations.id', $permitted_locations);
            }

            return Datatables::of($locations)
                ->addColumn(
                    'action',
                    '<button type="button" data-href="{{action(\'\Modules\Garage\Http\Controllers\GarageSettingsController@addimage\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("garage::lang.stamp_and_signature")</button>
                  

                    '
                )
                ->removeColumn('id')
                ->removeColumn('is_active')
                ->removeColumn('invoice_layout')
                ->removeColumn('invoice_scheme')
                ->removeColumn('price_group')
                ->removeColumn('country')
                ->removeColumn('state')
                ->removeColumn('zip_code')
                ->removeColumn('city')
                ->removeColumn('landmark')
                ->rawColumns([2])
                ->make(false);
        }
     } 
     
     
     public function addimage($id) {
        
       
        
        $td = BusinessLocation::where('id',$id)->first();
          
        return view('garage::locations.images')
                ->with(compact('td'));
        
    }
   
    public function saveimage(Request $request) {
        
   
          
     
     try {   
        $td = BusinessLocation::where('id',$request->jobcard_id)->first();
       
  
               if(!empty($request->stamp)) {
                $stamp = time() . '_' . $request->stamp->getClientOriginalName();
                if ($request->stamp->storeAs('location/', $stamp)) {
                     $td->stamp  = $stamp;
                }  
               }  
               
               if(!empty($request->signature)) {
                $signature = time() . '_' . $request->signature->getClientOriginalName();
                if ($request->signature->storeAs('location/', $signature)) {
                     
                }  
               }
         $td->garage_invoice_footer  = $request->garage_invoice_footer;
        $td->save();  
       
        
        $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
                
    } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }   
       return redirect()->back()->with(['success', __("lang_v1.success")]);
        
    
    }
    
   
     
     
}
