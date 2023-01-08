
<style>
    .whatsapp-box-link{
        text-align:center;
        background: #25d366;
        color: #fff;    
        border: 1px solid #287947;
        box-shadow: 2px 5px 5px #d4d4d4;
        padding: 7px 10px;
        border-radius: 3px;
        margin: 0;
        margin-bottom: 12px;
        height: 66px;
        display: block;
        line-height: 50px;
        font-size: 18px;
        transition: all .3s ease-in-out;
    }
    .whatsapp-box-link:hover{
        background: #1eb557;
        color: #fff;   
    }
    .youtube-box-link{
        text-align:center;
        background: #e62117;
        color: #fff;    
        border: 1px solid #8e130d;
        box-shadow: 2px 5px 5px #d4d4d4;
        padding: 7px 10px;
        border-radius: 3px;
        margin: 0;
        margin-bottom: 12px;
        height: 66px;
        display: block;
        line-height: 50px;
        font-size: 18px;
        transition: all .3s ease-in-out;
    }
    .youtube-box-link:hover{
        background: #c31b13;
        color: #fff;   
    }
    .blog-box-link{
        text-align:center;
        color: #fff;    
        border: 1px solid #174771;
        box-shadow: 2px 5px 5px #d4d4d4;
        padding: 7px 10px;
        border-radius: 3px;
        margin: 0;
        margin-bottom: 12px;
        height: 66px;
        display: block;
        line-height: 50px;
        font-size: 18px;
        transition: all .3s ease-in-out;
    }
    .blog-box-link:hover{
        color: #fff;   
    }
    .speed-click{
        display: inline-block !important;
        padding: 10px 15px !important;
        margin: 10px 12px;
        box-shadow: 2px 2px 5px #ccc;
        font-weight: bold;
        border-radius: 5px;
    }
    .row-custom .col-custom {
        display: block;
        margin-bottom: 25px;
    }
    .box, .info-box {
        margin-bottom: 13px;
    }
    .ads-slider .item,
    .ads-slider img{
        height: 300px;
    }
    .ads-slider-info{
        position: absolute;
        bottom: 0;
        text-align: center;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.5);
        color: #fff;
    }
    .ads-slider-info h3{
        color: #fff;
    }
    /*.ads-slider.owl-theme .owl-nav{*/
    /*    display: none;*/
    /*}*/
    .ads-slider.owl-theme .owl-dots, .owl-theme .owl-nav {
        margin-bottom: 20px;
        margin-top: 10px;
    }
    
    
    
    
    
    
    
    
    .home-info-box{
        padding: 15px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        width: 100%;
    }
    .home-info-box-1{
        background-image: linear-gradient(to left, #0db2de 0%, #005bea 100%) !important;
    }
    .home-info-box-2{
        background-image: linear-gradient(45deg, #f93a5a, #f7778c) !important;
    }
    .home-info-box-3{
        background-image: linear-gradient(to left, #48d6a8 0%, #029666 100%) !important;
    }
    .home-info-box-4{
        background-image: linear-gradient(to left, #efa65f, #f76a2d) !important;
    }
    .home-info-box-5{
        background-image: linear-gradient(to left, #5f71ef, #030885) !important;
    }
    .home-info-box .home-info-box-icon i{
        color: #fff;
        font-size: 26px;
    }
    .home-info-box .home-info-box-content > span{
        display: block;
    }
    .home-info-box .info-box-text{
        color: #fff;
        font-size: 15px;
        margin-bottom: 15px;
    }
    .home-info-box .info-box-number{
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        text-align: center;
    }
    
    .home-tables .box-header{
        padding-bottom: 0 !important;
    }
    .home-tables .box-body{
        padding-top: 0 !important;
    }
    .home-tables .box-header .box-title{
        font-size: 14px !important;
        font-weight: 700;
        color: #242f48 !important;
    }
    .home-tables .box-header .box-title i{
        top: 16px;
        font-size: 14px;
    }
    .home-tables .box-body > div{
        overflow-x: auto
    }
    .home-tables tr{
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    .home-tables .table>thead:first-child>tr:first-child>th{
        font-size: 13px;
        color: #37374e !important;
    }
    .home-tables tbody{
        max-height: 250px;
        overflow: auto;
        display: block;
    }
    .home-tables tr.odd{
        background: #ECF0FA;
    }
    .home-tables .table td{
        font-size: 11px;
        font-weight: 600;
        padding: 10px;
    }
    
    .row.mx-0{
        margin-inline: 0;
    }
    .home-fast-btns{
        margin-top: 20px;
        padding: 15px 8px;
    }
    .home-fast-btns a{
        background: #fff;
        padding: 6px 15px !important;
        font-weight: 600;
        color: #370971;
        border: 1px solid #370971;
        margin: 4px 5px;
        transition: all .2s ease-in;
    }
    /*.home-fast-btns a:nth-of-type(1){*/
    /*    color: #28b97b;*/
    /*    border: 1px solid #28b97b;*/
    /*}*/
    .home-fast-btns a:hover{
        background: #370971;
        color: #fff;
    }
</style>

@extends('layouts.app')
@section('title', __('garage::lang.garage') . ' '. __('business.dashboard'))

@section('content')
@include('garage::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
        {{$company->name ?? __('garage::lang.total_cash') }}
    <!--@lang('business.dashboard')	-->
    	<small>@lang('garage::lang.garage')</small>
    </h1>
</section>
<!-- Main content -->
<section class="content content-custom no-print">
    	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="btn-group pull-left" data-toggle="buttons">
			    	<div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
				<label class="btn btn-info active button4" style="background-color:#f14e4e">
    				<input type="radio" name="date-filter"
    				data-start="{{ date('Y-m-d') }}" 
    				data-end="{{ date('Y-m-d') }}"
    				checked> {{ __('home.today') }}
  				</label>
  				<label class="btn btn-info button4" style="background-color:#f1bb4e">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_week']['start']}}" 
    				data-end="{{ $date_filters['this_week']['end']}}"
    				> {{ __('home.this_week') }}
  				</label>
  				<label class="btn btn-info button4" style="background-color:#4e9af1">
    				<input type="radio" name="date-filter"
    				data-start="{{ $date_filters['this_month']['start']}}" 
    				data-end="{{ $date_filters['this_month']['end']}}"
    				> {{ __('home.this_month') }}
  				</label>
  				<label class="btn btn-info button4" style="background-color:#9a4ef1">
    				<input type="radio" name="date-filter" 
    				data-start="{{ $date_filters['this_fy']['start']}}" 
    				data-end="{{ $date_filters['this_fy']['end']}}" 
    				> {{ __('home.this_fy') }}
  				</label>
            </div>
		</div>
	</div>
	<div class="row">
	    
    	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-custom">
	      <div class="home-info-box home-info-box-1">
	        <span class="home-info-box-icon"><i class="ion ion-cash"></i></span>
  <a href="{{action('\Modules\Garage\Http\Controllers\DashboardController@getallTotal',['status'=>  0 ,'id'=>  $id])}}" style="color:white">
	        <div class="home-info-box-content">
	          <span class="info-box-text">
	            
	            	@lang('garage::lang.all_job_card')
	        
	          
	          </span>
	           <span class="info-box-number all_total"><i class="fas fa-sync fa-spin fa-fw "></i></span>
	        </div>
	       </a>
	      </div>
	   
	    </div>
	    
	    

	    
	      @foreach($status as $company)
	        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-custom">
	      <div class="home-info-box home-info-box-1">
	        <span class="home-info-box-icon"><i class="fa fas fa-truck"></i></span>
 <a href="{{action('\Modules\Garage\Http\Controllers\DashboardController@getallTotal',['status'=> $company->id , 'id'=>  $id])}}" style="color:white">
	        <div class="home-info-box-content">
	          <span class="info-box-text">{{ empty( $company->business_id ) ? __('garage::lang.'.$company->name.'') :  $company->view_name }}</span>
	          <span class="info-box-number {{ $company->name }}"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
	        </div>
	      </a>  
	      </div>
	   
	    </div>
	    @endforeach
	    
	    
	<!--	<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h4 class="box-title">@lang('garage::lang.job_sheets_by_status')</h4>
				</div>
				<div class="box-body">
					<div class="row">
				        @forelse($job_sheets_by_status as $job_sheet)
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="small-box" style="background-color: {{$job_sheet->color}};color: #fff;">
						            <div class="inner">
						              	<p>{{$job_sheet->status_name}}</p>
						              	<h3>{{$job_sheet->total_job_sheets}}</h3>
						            </div>
					          	</div>
					        </div>
					    @empty
					    	<div class="col-md-12">
	    						<div class="alert alert-info">
					                <h4>@lang('garage::lang.no_report_found')</h4>
					            </div>
				           	</div>
						@endforelse
					</div>
				</div>
			</div>
		</div>-->
	</div>
	<!--@if(in_array('service_staff', $enabled_modules))
		<div class="row">
		    <div class="col-xs-12">
		        @component('components.widget')
		            @slot('title')
		                @lang('garage::lang.job_sheets_by_service_staff')
		            @endslot
		            <div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>@lang('restaurant.service_staff')</th>
									<th>@lang('garage::lang.total_job_sheets')</th>
								</tr>
							</thead>
							<tbody>
								@foreach($job_sheets_by_service_staff as $job_sheet)
									<tr>
										<td>{{$loop->iteration}}</td>
										<td>{{$job_sheet->service_staff}}</td>
										<td>{{$job_sheet->total_job_sheets}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
		        @endcomponent
		    </div>
		</div>
	@endif-->

</section>
@stop
@section('javascript')

   <script>
        
        $(document).ready(function() {
    var start = $('input[name="date-filter"]:checked').data('start');
    var end = $('input[name="date-filter"]:checked').data('end');
       var  location_id = $('#location_id').val();
    update_statistics(start, end,location_id);
    $(document).on('change', 'input[name="date-filter"]', function() {
        var start = $('input[name="date-filter"]:checked').data('start');
        var end = $('input[name="date-filter"]:checked').data('end');
        update_statistics(start, end,location_id);
    });

     $(document).on('change', '#location_id',  function() {
         location_id = $(this).val();
      update_statistics(start, end,location_id); 
     });
  
});

function update_statistics(start, end,location_id = null) {
    var data = { start: start, end: end , location_id: location_id};
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    
    
       $('.total_cash').html(loader);
       $('.all_total').html(loader);
   @foreach($status as $company) 
    $('.{{ $company->name }}').html(loader);

    @endforeach
    
    $.ajax({
        method: 'get',
        url: '/garage/home/get-company-totals/{{$id}}',
        dataType: 'json',
        data: data,
        success: function(data) {
          

            //sell details
          $('.all_total').html(data.all_total);
              @foreach($status as $company) 
          
            $('.{{ $company->name }}').html(data.{{ $company->name }});
           @endforeach
        },
    });
}

        
    </script>

@endsection