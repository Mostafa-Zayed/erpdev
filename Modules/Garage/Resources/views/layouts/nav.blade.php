<div class="d-flex align-items-top justify-content-between no-print">
    <nav class="navbar navbar-expand-lg bg-white mb-4 p-0 inner-nav">
        <div class="container-fluid p-0">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <i class="fas fa-list"></i>
            </button>
            <div class="collapse navbar-collapse px-0 mx-0" id="bs-example-navbar-collapse-1">
                <div class="navbar-nav">
                       @if(auth()->user()->can('garage.view'))
                    <a href="{{action('\Modules\Garage\Http\Controllers\DashboardController@index')}}" @if(request()->segment(1) == 'garage' && request()->segment(2) == 'dashboard') class="active-dark nav-link" @else class="nav-link" @endif><i class="fas fa-wrench"></i>
                        {{__('garage::lang.garage')}}
                    </a>
                       @endif
                    
                    @can('garage.create')
                        <a href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@create')}}" @if(request()->segment(2) == 'job-sheet' && request()->segment(3) == 'create') class="active-dark nav-link" @else class="nav-link" @endif><i class="fas fa-wrench"></i>
                        {{__('garage::lang.add_job_sheet')}}
                        </a>
                    @endcan
                    @if(auth()->user()->can('garage.view'))
                        
                        <a href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@index')}}" @if(request()->segment(2) == 'job-sheet' && empty(request()->segment(3))) class="active-dark nav-link" @else class="nav-link" @endif><i class="fas fa-wrench"></i>
                            @lang('garage::lang.job_sheets')
                        </a>
                    @endif
                    
                    
                    @can('garage.view')  
                    
                    <a href="{{action('\Modules\Garage\Http\Controllers\InvoiceController@index')}}" @if(request()->segment(1) == 'garage' && request()->segment(2) == 'invoices') class="active-dark nav-link" @else class="nav-link" @endif><i class="fas fa-wrench"></i>@lang('garage::lang.invoices')</a>
                    @endcan
                    @if (auth()->user()->can('garage.settings'))
                    
                    <a href="{{action('\Modules\Garage\Http\Controllers\GarageSettingsController@index')}}" @if(request()->segment(1) == 'garage' && request()->segment(2) == 'garage-settings') class="active-dark nav-link" @else class="nav-link" @endif><i class="fas fa-wrench"></i>@lang('messages.settings')</a>
                    @endif                           
                </div>
            </div>
        </div>
    </nav>
</div>

<!--<section class="no-print" style="padding-top: 10px;padding-left: 10px;">-->
    

<!--</section>-->