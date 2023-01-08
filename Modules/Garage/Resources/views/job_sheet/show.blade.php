@extends('layouts.app')

@section('title', __('garage::lang.view_job_sheet'))

@section('content')
@include('garage::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('garage::lang.job_sheet')
    	(<code>{{$job_sheet->job_sheet_no}}</code>)
    </h1>
</section>



<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid" id="job_sheet">
				<div class="box-header no-print">
					<div class="box-tools">
						<a href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@edit', ['id' => $job_sheet->id])}}" class="btn btn-info cursor-pointer">
		                    <i class="fa fa-edit"></i>
		                    @lang("messages.edit")
		                </a>
						<button type="button" class="btn btn-primary" aria-label="Print" id="print_jobsheet">
							<i class="fa fa-print"></i>
							@lang( 'garage::lang.print_format_1' )
				      	</button>

				      	<a class="btn btn-success" href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@print', ['id' => $job_sheet->id])}}" target="_blank">
							<i class="fas fa-file-pdf"></i>
							@lang( 'garage::lang.print_format_2' )
				      	</a> 	
				      	
				       <a href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@car_work', ['id' => $job_sheet->id]) }}" class="cursor-pointer print_insurance btn btn-primary"  target="_blank">
                                        <i class="fa fa-print"></i>
                                       {{__("garage::lang.car_work")}}
                           </a>
				      	@if($job_sheet->estimation == 1)
				      	<a class="btn btn-success" href="{{action('\Modules\Garage\Http\Controllers\JobSheetController@print_estimation', ['id' => $job_sheet->id])}}" target="_blank">
							<i class="fas fa-file-pdf"></i>
							@lang( 'garage::lang.print_estimation' )
				      	</a>
				      	@endif
			      </div>
			    </div>
				<div class="box-body">
					{{-- business address --}}
					<div class="row invoice-info">
						<div class="col-md-6 invoice-col" style="padding-top: 0px;">
							@if(!empty(Session::get('business.logo')))
			                  <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 120px; margin: auto;">
			                @endif
						</div>
						<div class="col-md-6 bizz_addr">
							<p style="text-align: center;padding-top: 40px;padding-left: 110px;">
								<strong class="font-23">
									{{optional($job_sheet->businessLocation)->name}}
								</strong>
								<br>
								<span>
								<!--	{!!$job_sheet->customer->business->business_address!!}-->
									{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
								</span>
									<br>
			Telephone:{{optional($job_sheet->businessLocation)->mobile}}
							</p>
						</div>	
					</div>
					{{-- Job sheet details --}}
					<table class="table table-bordered" style="margin-top: 15px;">
						
<!--start print table view -->						
						
						
						
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<style type="text/css">
	.box {
		border: 1px solid;
	}
	.table-pdf {
		width: 100%;
	}

	.table-pdf td, .table-pdf th {
		padding: 6px;
		text-align: left;
	}
	.w-20 {
		width: 20%;
		float: left;
	}
	.checklist {
		padding: 5px 15px;
		width: 100%;
	}
	.checkbox {
		width: 20%;
		float: left;
	}
	.checkbox-text {
		width: 80%;
		float: left;
	} 
	.content-div {
		padding: 6px;
	}
	.table-slim{
		width: 100%;
	}

	.table-slim td, .table-slim th {
		padding: 1px !important;
		font-size: 12px;
	}
	.font-14 {
		font-size: 14px;
	}
	.font-12 {
		font-size: 12px;
	}
	body {
		font-size: 11px;
	}
</style>



<div class="width-100 box mb-10">
	<table class="no-border table-pdf">
		<tr>
			
			<th>@lang('garage::lang.pay_types'):</th>
			<th>@lang('garage::lang.job_sheet_no'):</th>
			<th rowspan="2">
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($job_sheet->job_sheet_no, 'C128', 1,50,array(39, 48, 10), true)}}">
			</th>
			<th>
			    	
		    @lang('receipt.date'):
	
		
			{{@format_datetime($job_sheet->created_at)}}
		
			</th>
		
		</tr>
		<tr>
		
			<td style="padding-top: -8">@lang('garage::lang.'.$job_sheet->pay_types)</td>
			<td style="padding-top: -8">{{$job_sheet->job_sheet_no}}</td>
	
		</tr>
	</table>
</div>
<div class="box mb-10">
<table class="table-pdf">
    <tr>
        <td></td> 
    <td style="font-size:16px;padding-right:12px">
    Customer and vehicle info
    </td>
    <td></td> 
    </tr>
	<tr>
		<td style="vertical-align: top;">
			<table class="width-100">
				<tr>
					<th style="padding-left: 0;">Name: </th>
					<td>{{$job_sheet->customer->name}}</td>
				</tr>
				<tr>
					<th>Vehicle:</th>
					<td>{{optional($job_sheet->brand)->name}}</td>
			
				</tr>
			
			</table>
		</td>
		<td colspan="2" style="vertical-align: top;">
			<table class="width-100">
			    <tr>
			        <th>Mobile</th>
			        <td>{{$job_sheet->customer->mobile}}</td>
			        <th>@lang('garage::lang.car_status'):</th>
			        <td colspan="2">	@lang('garage::lang.'.$job_sheet->car_status) {{!empty($job_sheet->date_in) ? @format_datetime($job_sheet->date_in) : ''}} </td>
			    </tr>
			
				<tr>
				    <th>@lang('garage::lang.model'):</th>
					<td>{{$job_sheet->care_model}}</td>
					<th>@lang('garage::lang.car_plate'):</th>
					<td>{{$job_sheet->car_plate}}</td>
				</tr>
				<tr>
					
				<!--<th>	@lang('garage::lang.type'):</th>
			    <td colspan="2">{{$job_sheet->type}}</td>-->
				</tr>
			
			
			</table>
		</td>
	</tr>

</table>

</div>
<div class="box mb-10" style="text-align: center;">

<div class="width-100 content-div">
	<div class="width-100">
	 <div class="box-header with-border">
		            <h3 class="box-title">{{ __('garage::lang.preview') }}</h3>
		        </div>
		        <!-- /.box-header -->
		        <img src="{{   asset('/uploads/job_card/' . $job_sheet->car_marks)}}" style="width: 50%;">
		          <p><span style="color:#ff0000;"><em class="icon ni ni-circle-fill"></em> Red color</span> for accident  and <span style="color:#1418FF;"><em class="icon ni ni-circle-fill"></em> Blue color</span> for dents.</p>
	</div>

</div>


</div>



  @if($job_sheet->pay_types == 'insurance' || $job_sheet->pay_types == 'both')
<div class="box mb-10">
<table class="table-pdf" style="text-align:center;" >
    <tr>
   
    <td style="font-size:16px;text-align:center" colspan="2">
    Insurance info
    </td>
    
    </tr>
	<tr>
		<td style="vertical-align: top;">
			<table class="width-100">
				<tr>
					<td><strong>Company:</strong>&nbsp; {{optional($job_sheet->company)->name}}</td>
					<td><strong>Accident type (OD-REC-TP):</strong>&nbsp;{{$job_sheet->type}}</td>
			        <td style="text-align: end;"><strong>Excess (Yes or No):</strong> &nbsp; {{$job_sheet->excess}}</td>
				</tr>
				<tr style="width:100%">
			        <td><strong>@lang('garage::lang.insurance_desc'):</strong> &nbsp;{!!$job_sheet->insurance_desc!!}</td>
			    </tr>
			</table>
		</td>
	
	
	</tr>
</table>
</div>
@endif


  
  	@if($job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both')
  	<div class="box mb-10">
        <table class="table-pdf" >
            <tr>
            
            <td style="font-size:16px;text-align:center" colspan="2">Cash info</td>
           
            </tr>
        	<tr>
        		<td style="vertical-align: top;">
        			<table class="width-100">
        				<tr>
        			        <td><strong>Amount :</strong>&nbsp;	<span class="display_currency" data-currency_symbol="true">
			{{$job_sheet->cash_cost}}	
			</span></td>
        			        <td><strong>Advance :</strong>&nbsp;	<span class="display_currency" data-currency_symbol="true">
		{{$job_sheet->deposit }}	
			</span></td>
        			        <td><strong>Balance :</strong>&nbsp;<span class="display_currency" data-currency_symbol="true">
			 {{	$job_sheet->cash_cost -  $job_sheet->deposit}}
			</span></td>
        			        <tr>
        			        <td><strong>@lang('garage::lang.cash_desc'):</strong>&nbsp; {!!$job_sheet->cash_desc!!} </td>
        			        </tr>
        				</tr>
        			</table>
        		</td>
        	</tr>
        </table>
</div>

@endif 	




			<div class="row">
	    
	    
		@if($job_sheet->media->count() > 0)
			<div class="col-md-6">
				<div class="box box-solid no-print">
					<div class="box-header with-border">
						<h4 class="box-title">
							@lang('garage::lang.uploaded_image_for', ['job_sheet_no' => $job_sheet->job_sheet_no])
						</h4>
				    </div>
					<div class="box-body">
						@includeIf('garage::job_sheet.partials.document_table_view', ['medias' => $job_sheet->media])
					</div>
				</div>
			</div>
		@endif
		
		<div class="col-md-6">
				<div class="box box-solid no-print">
					<div class="box-header with-border">
						<h4 class="box-title">
							@lang('garage::lang.papers')
						</h4>
				    </div>
					<div class="box-body">
						@includeIf('garage::job_sheet.partials.papers_table_view')
					</div>
				</div>
			</div>

		
		<div class="col-md-6">
			<div class="box box-solid box-solid no-print">
		        <div class="box-header with-border">
		            <h3 class="box-title">{{ __('garage::lang.activities') }}:</h3>
		        </div>
		        <!-- /.box-header -->
		        @include('garage::repair.partials.activities')
		    </div>
		</div>
	</div>		
					
				</div>
			</div>
		</div>
	</div>
	
</section>













<!-- /.content -->
@stop
@section('css')
<style type="text/css">
	.table-bordered>thead>tr>th, .table-bordered>tbody>tr>th,
	.table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td,
	.table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
		border: 1px solid #1d1a1a;
	}
	@media print{
		.bizz_addr {
			float: right;
		}
	}
</style>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		$('#print_jobsheet').click( function(){
			$('#job_sheet').printThis();
		});
		$(document).on('click', '.delete_media', function (e) {
            e.preventDefault();
            var url = $(this).data('href');
            var this_btn = $(this);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if(result.success == true){
			                    this_btn.closest('tr').remove();
			                    toastr.success(result.msg);
			                } else {
			                    toastr.error(result.msg);
			                }
                        }
                    });
                }
            });
        });
	});
</script>
@stop