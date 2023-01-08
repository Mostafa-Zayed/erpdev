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
			        <td><strong>Excess (Yes or No):</strong> &nbsp; {{$job_sheet->excess}}</td>
				</tr>
				<tr>
			        <td><strong>@lang('garage::lang.insurance_desc'):</strong> &nbsp;{!!$job_sheet->insurance_desc!!}</td>
			    </tr>
			</table>
		</td>
	
	
	</tr>
</table>
</div>

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
						<div class="col-md-6 invoice-col" style="padding-top: 40px;">
							@if(!empty(Session::get('business.logo')))
			                  <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
			                @endif
						</div>
						<div class="col-md-6 bizz_addr">
							<p style="text-align: center;padding-top: 40px;padding-left: 110px;">
								<strong class="font-23">
									{{$job_sheet->customer->business->name}}
								</strong>
								<br>
								<span>
									{!!$job_sheet->customer->business->business_address!!}
								</span>
							</p>
						</div>	
					</div>
					{{-- Job sheet details --}}
					<table class="table table-bordered" style="margin-top: 15px;">
						<tr>
							<th rowspan="3">
								@lang('receipt.date'):
								<span style="font-weight: 100">
								    @if(!empty($job_sheet->date_in))
								    	{{@format_datetime($job_sheet->date_in)}}
								    @else
								    
								    	{{@format_datetime($job_sheet->created_at)}}
								    @endif
								
								</span>
							</th>
						</tr>
						<tr>
							<td>
								<b>@lang('garage::lang.pay_types'):</b>
								@lang('garage::lang.'.$job_sheet->pay_types)
							</td>
							<th rowspan="2">
								<b>
									@lang('garage::lang.expected_delivery_date'):
								</b>
								@if(!empty($job_sheet->completed_on))
									<span style="font-weight: 100">
										{{@format_datetime($job_sheet->completed_on)}}
									</span>
								@endif
							</th>
						</tr>
						<tr>
							<td>
								<b>@lang('garage::lang.job_sheet_no'):</b>
								{{$job_sheet->job_sheet_no}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<strong>@lang('role.customer'):</strong><br>
								<p>
									{{$job_sheet->customer->name}} <br>
									{!! $job_sheet->customer->contact_address !!}
									@if(!empty($contact->email))
										<br>@lang('business.email'):
										{{$job_sheet->customer->email}}
									@endif
									<br>@lang('contact.mobile'):
									{{$job_sheet->customer->mobile}}
								
									@if(!empty($job_sheet->customer->garage_id_number))
										<br>@lang('contact.tax_no'):
										{{$job_sheet->customer->garage_id_number}}
									@endif
									
									@if(!empty($contact->tax_number))
										<br>@lang('contact.tax_no'):
										{{$job_sheet->customer->tax_number}}
									@endif
								</p>
							</td>
							<td>
								<b>@lang('product.brand'):</b>
								{{optional($job_sheet->brand)->name}}
								<br>
							
								<b>@lang('garage::lang.model'):</b>
								{{$job_sheet->care_model}}
								<br>
								<b>@lang('garage::lang.car_plate'):</b>
								{{$job_sheet->car_plate}}
								<br>
							
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('sale.invoice_no'):
								</b>
							</td>
							<td>
								@if($job_sheet->invoices->count() > 0)
									@foreach($job_sheet->invoices as $invoice)
										{{$invoice->invoice_no}}
										@if (!$loop->last)
									        {{', '}}
									    @endif
									@endforeach
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.estimated_cost'):
								</b>
							</td>
							<td>
								<span class="display_currency" data-currency_symbol="true">
									{{$job_sheet->cash_cost + $job_sheet->insurance_cost}}
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('sale.status'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->status)->name}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('business.location'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->businessLocation)->name}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.car_status'):
								</b>
							</td>
							<td>
								@lang('garage::lang.'.$job_sheet->car_status)
							</td>
						</tr>
					 	<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.type'):
								</b>
							</td>
							<td>
								@lang('garage::lang.'.$job_sheet->type)
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.technician'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->createdBy)->user_full_name}}
							</td>
						</tr>
					<!--	<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.pre_repair_checklist'):
								</b>
							</td>
							<td>
								@php
									$checklists = [];
									if (!empty($job_sheet->deviceModel) && !empty($job_sheet->deviceModel->repair_checklist)) {
										$checklists = explode('|', $job_sheet->deviceModel->repair_checklist);
									}
								@endphp
								@if(!empty($job_sheet->checklist))
									@foreach($checklists as $check)
			                            <div class="col-xs-4">
			                                @if($job_sheet->checklist[$check] == 'yes')
			                                    <i class="fas fa-check-square text-success fa-lg"></i>
			                                @elseif($job_sheet->checklist[$check] == 'no')
			                                  <i class="fas fa-window-close text-danger fa-lg"></i>
			                                @elseif($job_sheet->checklist[$check] == 'not_applicable')
			                                  <i class="fas fa-square fa-lg"></i>
			                                @endif
			                                {{$check}}
			                                <br>
			                            </div>
			                        @endforeach
			                    @endif
							</td>
						</tr>-->
						@if($job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both')
							<tr>
								<td colspan="3">
									<b>
										@lang('garage::lang.cash_desc'):
									</b> <br>
									{!!$job_sheet->cash_desc!!}
								</td>
							</tr>
						@endif	
						
						@if($job_sheet->pay_types == 'insurance' || $job_sheet->pay_types == 'both')
							<tr>
								<td colspan="3">
									<b>
										@lang('garage::lang.insurance_desc'):
									</b> <br>
									{!!$job_sheet->insurance_desc!!}
								</td>
							</tr>
						@endif
					<!--	<tr>
							<td colspan="3">
								<b>
									@lang('garage::lang.product_configuration'):
								</b> <br>
								@php
									$product_configuration = json_decode($job_sheet->product_configuration, true);
								@endphp
								@if(!empty($product_configuration))
									@foreach($product_configuration as $product_conf)
										{{$product_conf['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<b>
									@lang('garage::lang.condition_of_product'):
								</b> <br>
								@php
									$product_condition = json_decode($job_sheet->product_condition, true);
								@endphp
								@if(!empty($product_condition))
									@foreach($product_condition as $product_cond)
										{{$product_cond['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>
						@if(!empty($job_sheet->custom_field_1))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_1'] ?? __('lang_v1.custom_field', ['number' => 1])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_1}}
								</td>
							</tr>
						@endif
					</td>
				</tr>-->
				<tr>
					<th colspan="2">@lang('garage::lang.parts_used'):</th>
					<td>
						@if(!empty($job_sheet->parts_desc))	
						
						{!! $job_sheet->parts_desc !!}
						
						@elseif(!empty($parts))
						<table>
							@foreach($parts as $part)
								<tr>
									<td>{{$part['variation_name']}}: &nbsp;</td>
									<td>{{$part['quantity']}} {{$part['unit']}}</td>
								</tr>
							@endforeach
						</table>
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="3">
					<!--	<strong>
							@lang("lang_v1.terms_conditions"):
						</strong>
						@if(!empty($repair_settings['repair_tc_condition']))
							{!!$repair_settings['repair_tc_condition']!!}
						@endif-->
                        @if(!empty($job_sheet->custom_field_1))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_1'] ?? __('lang_v1.custom_field', ['number' => 1])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_1}}
								</td>
							</tr>
						@endif
						@if(!empty($job_sheet->custom_field_2))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_2'] ?? __('lang_v1.custom_field', ['number' => 2])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_2}}
								</td>
							</tr>
						@endif
						@if(!empty($job_sheet->custom_field_3))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_3'] ?? __('lang_v1.custom_field', ['number' => 3])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_3}}
								</td>
							</tr>
						@endif
						@if(!empty($job_sheet->custom_field_4))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_4'] ?? __('lang_v1.custom_field', ['number' => 4])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_4}}
								</td>
							</tr>
						@endif
						@if(!empty($job_sheet->custom_field_5))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_5}}
								</td>
							</tr>
						@endif
			<!--			<tr>
							<td colspan="3">
								<b>
									@lang('garage::lang.problem_reported_by_customer'):
								</b> <br>
								@php
									$defects = json_decode($job_sheet->defects, true);
								@endphp
								@if(!empty($defects))
									@foreach($defects as $product_defect)
										{{$product_defect['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>-->
						<tr>
							<td colspan="3">
								<strong>
									@lang("lang_v1.terms_conditions"):
								</strong>
								@if(!empty($repair_settings['repair_tc_condition']))
									{!!$repair_settings['repair_tc_condition']!!}
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('garage::lang.customer_signature'):
								</b>
							</td>
							<td>
								<b>
									@lang('garage::lang.authorized_signature'):
								</b>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
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
		            <h3 class="box-title">{{ __('garage::lang.preview') }}:</h3>
		        </div>
		        <!-- /.box-header -->
		        <img src="{{   asset('/uploads/job_card/' . $job_sheet->car_marks)}}">
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
</section>



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
	<div class="width-50 f-left" align="center">
		@if(!empty(Session::get('business.logo')))
          <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
        @endif
	</div>
	<div class="width-50 f-left" align="center">
		<p style="text-align: center;">
		    @lang('receipt.date'):
			@if(!empty($job_sheet->date_in))
			    {{@format_datetime($job_sheet->date_in)}}
			@else
			{{@format_datetime($job_sheet->created_at)}}
			@endif
			<br>
			Branch name:
			{{$job_sheet->customer->business->name}}
			<br>
			Location:
			{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
			<!--{!!$job_sheet->customer->business->business_address!!}-->
			<br>
			Telephone:{{optional($job_sheet->businessLocation)->mobile}}
			<!--<strong class="font-14">
				
			</strong>
			<br>
			<span class="font-12">
				{!!$job_sheet->customer->business->business_address!!}
			</span>-->
			
			
		</p>
	</div>
</div>
<div class="width-100 box mb-10">
	<table class="no-border table-pdf">
		<tr>
			
			<th>@lang('garage::lang.pay_types'):</th>
			<th>@lang('garage::lang.job_sheet_no'):</th>
			<th rowspan="2">
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($job_sheet->job_sheet_no, 'C128', 1,50,array(39, 48, 10), true)}}">
			</th>
			<!--<th>@lang('garage::lang.expected_delivery_date'):</th>-->
		</tr>
		<tr>
		
			<td style="padding-top: -8">@lang('garage::lang.'.$job_sheet->pay_types)</td>
			<td style="padding-top: -8">{{$job_sheet->job_sheet_no}}</td>
			<!--<td style="padding-top: -8">@if(!empty($job_sheet->completed_on))
								
										{{@format_datetime($job_sheet->completed_on)}}
								
								@endif</td>-->
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
				<!--	<th>@lang('garage::lang.serial_no'):</th>
			    <td colspan="2">{{$job_sheet->serial_no}}</td>-->
				</tr>
				<tr>
				<!--	<td style="padding-left: 0; padding-top: -5">
						<p>
							 <br>
							{!! $job_sheet->customer->contact_address !!}
							@if(!empty($contact->email))
								<br>@lang('business.email'):
								{{$job_sheet->customer->email}}
							@endif
							<br>
							
							@if(!empty($job_sheet->customer->garage_id_number))
										<br>@lang('contact.tax_no'):
										{{$job_sheet->customer->garage_id_number}}
									@endif
							@if(!empty($contact->tax_number))
								<br>@lang('contact.tax_no'):
								{{$job_sheet->customer->tax_number}}
							@endif
						</p>
					</td>-->
				</tr>
			</table>
		</td>
		<td colspan="2" style="vertical-align: top;">
			<table class="width-100">
			    <tr>
			        <th>Mobile</th>
			        <td>{{$job_sheet->customer->mobile}}</td>
			        <th>@lang('garage::lang.car_status'):</th>
			        <td colspan="2">	@lang('garage::lang.'.$job_sheet->car_status)</td>
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
<!--	<tr>
		<td style="padding-top: 0">
			<strong>@lang('sale.invoice_no'):</strong>
			@if($job_sheet->invoices->count() > 0)
				@foreach($job_sheet->invoices as $invoice)
					{{$invoice->invoice_no}}
					@if (!$loop->last)
				        {{', '}}
				    @endif
				@endforeach
			@endif
		</td>
		<td style="padding-top: 0">
			<strong>@lang('garage::lang.estimated_cost'):</strong>
			<span class="display_currency" data-currency_symbol="true">
				@format_currency($job_sheet->cash_cost + $job_sheet->insurance_cost)
			</span>
		</td>
		<td style="padding-top: 0">
			<strong>
				@lang('sale.status'):
			</strong>
			    {{optional($job_sheet->status)->name}}
		</td>
	</tr>-->
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
		          <p><span style="color:#ff0000;"><em class="icon ni ni-circle-fill"></em> Red color</span> for dents and <span style="color:#1418FF;"><em class="icon ni ni-circle-fill"></em> Blue color</span> for scratches.</p>
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
				@format_currency($job_sheet->cash_cost )
			</span></td>
        			        <td><strong>Advance :</strong>&nbsp;	<span class="display_currency" data-currency_symbol="true">
				@format_currency($job_sheet->deposit )
			</span></td>
        			        <td><strong>Balance :</strong>&nbsp;<span class="display_currency" data-currency_symbol="true">
				@format_currency($job_sheet->cash_cost -  $job_sheet->deposit )
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




<!--<div class="box mb-10"><div class="width-100 content-div">
	<strong>@lang('garage::lang.product_configuration'):</strong>
	@php
		$product_configuration = json_decode($job_sheet->product_configuration, true);
	@endphp
	@if(!empty($product_configuration))
		@foreach($product_configuration as $product_conf)
			{{$product_conf['value']}}
			@if(!$loop->last)
				{{','}}
			@endif
		@endforeach
	@endif
</div>
<div class="width-100 content-div">
	<strong>@lang('garage::lang.condition_of_product'):</strong>
	@php
		$product_condition = json_decode($job_sheet->product_condition, true);
	@endphp
	@if(!empty($product_condition))
		@foreach($product_condition as $product_cond)
			{{$product_cond['value']}}
			@if(!$loop->last)
				{{','}}
			@endif
		@endforeach
	@endif
</div>
<div class="width-100 content-div">
	<strong>@lang('garage::lang.problem_reported_by_customer'):</strong>
	@php
		$defects = json_decode($job_sheet->defects, true);
	@endphp
	@if(!empty($defects))
		@foreach($defects as $product_defect)
			{{$product_defect['value']}}
			@if(!$loop->last)
				{{','}}
			@endif
		@endforeach
	@endif
</div>-->

<!--<div class="width-100 content-div">
	@if(!empty($job_sheet->custom_field_1))
	<div class="width-50 f-left mb-5">
		<strong>{{$repair_settings['job_sheet_custom_field_1'] ?? __('lang_v1.custom_field', ['number' => 1])}}:</strong> 
	{{$job_sheet->custom_field_1}}
	</div>
	@endif
	@if(!empty($job_sheet->custom_field_2))
	<div class="width-50 f-left mb-5">
			<strong>{{$repair_settings['job_sheet_custom_field_2'] ?? __('lang_v1.custom_field', ['number' => 2])}}:</strong> 
			{{$job_sheet->custom_field_2}}
	</div>
	@endif
	@if(!empty($job_sheet->custom_field_3))
	<div class="width-50 f-left">
		<strong>{{$repair_settings['job_sheet_custom_field_3'] ?? __('lang_v1.custom_field', ['number' => 3])}}:</strong> 
		{{$job_sheet->custom_field_3}}
	</div>
	@endif
	@if(!empty($job_sheet->custom_field_4))
	<div class="width-50 f-left mb-5">
		<strong>{{$repair_settings['job_sheet_custom_field_4'] ?? __('lang_v1.custom_field', ['number' => 4])}}:</strong> 
		{{$job_sheet->custom_field_4}}
	</div>
	@endif
	@if(!empty($job_sheet->custom_field_5))
	<div class="width-50 f-left mb-5">
		<strong>{{$repair_settings['job_sheet_custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5])}}:</strong> 
		{{$job_sheet->custom_field_5}}
	</div>
	@endif
</div>
</div>-->
<!--<div class="box">
<table class="table-pdf">
	<tr>
		<th>@lang('garage::lang.parts_used'):</th>
		<td>
				@if(!empty($job_sheet->parts_desc))	
						
						{!! $job_sheet->parts_desc !!}
						
			 	@elseif(!empty($parts))
				<table class="table-slim">
					@foreach($parts as $part)
						<tr>
							<td>{{$part['variation_name']}}: &nbsp;</td>
							<td>{{$part['quantity']}} {{$part['unit']}}</td>
						</tr>
					@endforeach
				</table>
			@endif
		</td>
	</tr>
</table>
</div>-->
<div class="width-100 content-div" style="text-align:center;">
	<strong>@lang("lang_v1.terms_conditions")</strong>
@if( App::getLocale() == 'ar')
	@if(!empty($repair_settings['repair_tc_condition_ar']))
		{!!$repair_settings['repair_tc_condition_ar']!!}
	@endif	
@else	
	@if(!empty($repair_settings['repair_tc_condition']))
		{!!$repair_settings['repair_tc_condition']!!}
	@endif	
	
@endif
</div>
<table class="table-pdf">
	<tr>
		<th>
			@lang('garage::lang.customer_signature'):
		</th>
		
	</tr>
</table><br>
<span style='font-size:20px;'>&#9986; ------------------------------------------------------------------------------------------------------</span>

<table class="table-pdf">
	<tr>
		<td><strong>@lang('garage::lang.job_sheet_no'):</strong><br>
			{{$job_sheet->job_sheet_no}}
		</td>
		<td><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($job_sheet->job_sheet_no, 'C128', 1,50,array(39, 48, 10), true)}}"></td>
		<td>
		    	<strong>@lang('product.brand'):</strong>  {{optional($job_sheet->brand)->name}} &nbsp;<br>
			<strong>@lang('garage::lang.model'):</strong>  {{$job_sheet->care_model}} &nbsp;<br>
			<strong>@lang('garage::lang.car_plate'):</strong>  {{$job_sheet->car_plate}} &nbsp;<br>
		
		<!--	<strong>@lang('lang_v1.password'):</strong> {{$job_sheet->security_pwd}}-->
			<!--	<strong>@lang('garage::lang.serial_no'): </strong>{{$job_sheet->serial_no}} <br>
		<strong>@lang('garage::lang.security_pattern_code'):</strong>
			{{$job_sheet->security_pattern}}-->
		</td>
	</tr>
<!--	<tr>
		<td><strong>@lang('garage::lang.expected_delivery_date'):</strong><br>@if(!empty($job_sheet->completed_on))
								
										{{@format_datetime($job_sheet->completed_on)}}
								
								@endif</td>
		<td colspan="2">
			<strong>@lang('garage::lang.problem_reported_by_customer'):</strong> <br>
			@php
				$defects = json_decode($job_sheet->defects, true);
			@endphp
			@if(!empty($defects))
				@foreach($defects as $product_defect)
					{{$product_defect['value']}}
					@if(!$loop->last)
						{{','}}
					@endif
				@endforeach
			@endif
		</td>
	</tr>-->
</table>












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