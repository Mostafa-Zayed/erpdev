<?php

namespace Modules\Garage\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Controller;

use Modules\Garage\Entities\CarBrand;

use Yajra\DataTables\Facades\DataTables;
use Modules\Garage\Utils\GarageUtil;

class CarBrandController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, GarageUtil $repairUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->repairUtil = $repairUtil;
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

        if (request()->ajax()) {
            $statuses = CarBrand::where('business_id', $business_id)
                        ->select(['name','id']);

            return Datatables::of($statuses)
                ->editColumn('name', '
                    {{$name}}
                  
                ')
         
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Garage\Http\Controllers\CarBrandController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>'
                )
                ->removeColumn(['id'])
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();

        return view('garage::CarBrand.create')
            ->with(compact('status_template_tags'));
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
            $input = $request->only(['name']);
                    
                    
       
            $input['business_id'] = $business_id;

            $status = CarBrand::create($input);

            $output = ['success' => true,
                        'msg' => __("lang_v1.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.settings')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $status = CarBrand::where('business_id', $business_id)->find($id);
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();

            return view('garage::CarBrand.edit')
                ->with(compact('status', 'status_template_tags'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'garage_module') && auth()->user()->can('garage.settings')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name']);
                    
              
                $status = CarBrand::where('business_id', $business_id)->findOrFail($id);
                $status->update($input);

                $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
