<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<style type="text/css">
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
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
	
		
	
body{
    min-height: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}
.main-container{
    z-index: 9999;
    background: #fff;
    flex-grow: 1;
}
footer{
    flex-shrink: 0;
}
	
@media print{
 footer{
     position:fixed;
     bottom:0;
     }
}
</style>

<div class="main-container" style="background: #fff">
    <div class="width-100 ">
    	<div class="width-50 f-left" align="left">
    		@if(!empty(Session::get('business.logo')))
              <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
            @endif
    	</div>
    	<div class="width-50 f-left" align="left">
    		<p style="text-align:left; font-weight:100;">
    
    			<br>
    			{{optional($job_sheet->businessLocation)->name}}
    			<br>
    			Location:
    			{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
    
    			<br>
    			Telephone:{{optional($job_sheet->businessLocation)->mobile}}
    			<br>
    			Landline:042633384
    		</p>
    	</div>
    </div>
    <div class="width-100 ">
    	<table class=" table-pdf" >
    		
    			<tr>
    			    
        			<th style="background-color:#E6E6E6;">@lang('garage::lang.pay_types')</th>
        			<th><span  style="font-weight: normal;">@lang('garage::lang.'.$job_sheet->pay_types)</span></th>
    			    <td colspan="2" style="text-align:center;background-color:#E6E6E6;" >
    				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($job_sheet->job_sheet_no, 'C128', 1,50,array(39, 48, 10), true)}}" style="text-align:right;height:30px;">
    				</td>
    			</tr>
    			<tr>
        			<td style="background-color:#E6E6E6;" ><span style="font-weight: bold;">@lang('receipt.date')</span></td>
                    <td>{{@format_datetime($job_sheet->created_at)}}</td>
                    <td style="background-color:#E6E6E6;border-top:none;"><span style="font-weight:bold;">@lang('garage::lang.job_sheet_no')</span></td>
                    <td >{{$job_sheet->job_sheet_no}}</td>
                </tr>
    		
    	
    		
    	
    
    	</table>
    </div>
    
        
    <table class="table-pdf">
        <tr>
            <th style="text-align:center;background-color:#d9d9d9;"><p style="font-weight:bold;font-size:12px;">Customer and vehicle info</p></th>
        </tr>
    	<tr>
    		<td style="vertical-align: top;">
    			<table class="width-100">
    				<tr>
    					<th  style="border-bottom: none;background-color:#E6E6E6;width:20%;">Name:</th>
    	                <th style="font-weight: normal;">{{$job_sheet->customer->name}}</th>
    			        <th style="width:20%;border-bottom: none;background-color:#E6E6E6">phone:</th>
    			        <th style="font-weight: normal;">{{$job_sheet->customer->mobile}}</th>
    			    </tr> 
    			    <tr>
    			          	<td style="background-color:#E6E6E6;font-weight: bold;">Vehicle:</td>
    			          	<td>{{optional($job_sheet->brand)->name}}</td>
    			          	<td style="background-color:#E6E6E6;font-weight: bold;">@lang('garage::lang.model'):</td>
    			          	<td>{{$job_sheet->care_model}}</td>
    			    </tr> 
    			    <tr>
    			          	<td style="background-color:#E6E6E6;font-weight: bold;">@lang('garage::lang.car_plate'):</td>
    			          	<td>{{$job_sheet->car_plate}}</td>
    			          	<td style="background-color:#E6E6E6;font-weight: bold;">@lang('garage::lang.car_status'):</td>
    			          	<td><span  style="text-align:left; font-weight: bold; color:green;">@lang('garage::lang.'.$job_sheet->car_status)</span>
    			            <span  style="text-align:left; font-weight: bold; color:red;">{{!empty($job_sheet->date_in) ? @format_datetime($job_sheet->date_in) : ''}}</span></td>
    			    </tr>
    			</table>
    		</td>
    	</tr>
    
    </table>
    
    <div  class="box mb-10" style="text-align: center;">
    <p  style="font-weight:bold;font-size:14px;text-align:center;border: 1px solid;background-color: #c3c2c2;margin: 0;line-height: 2;">{{ __('garage::lang.preview') }}</p>
    <div class="width-100 content-div">
        
    	<div class="width-100" style="background-color:#f7f7f7;">
    		        <!-- /.box-header -->
    		        <img src="{{   asset('/uploads/job_card/' . $job_sheet->car_marks)}}" style="width: 50%;">
    		          <p><span style="color:#ff0000;"><em class="icon ni ni-circle-fill"></em> Red color</span> for accident and <span style="color:#1418FF;"><em class="icon ni ni-circle-fill"></em> Blue color</span> for dents.</p>
    	</div>
    
    </div>
    
    
    </div>
    
    
    
      @if($job_sheet->pay_types == 'insurance' || $job_sheet->pay_types == 'both')
        
            <!--<p  style="font-weight: bold;font-size:16px; color:green; text-align:center;">Insurance info</p>-->
            <table class="table-pdf width-100">
                    <tr>
                        <th style="text-align:center;background-color:#d9d9d9;border-style: none" colspan="6">Insurance info</th>
                    </tr>
    	
    				<tr>
    					<th style="width:16.5%"><strong>Company:</strong></th>
    					<td style="width:16.5%">{{optional($job_sheet->company)->name}}</td>
    					<th style="width:16.5%"><strong>Excess:</strong></th>
    					<td style="width:16.5%">{{$job_sheet->excess}}</td>				
    					<th style="width:16.5%"><strong>Accident type:</strong></th>
    					<td style="width:16.5%">{{$job_sheet->type}}</td>
    
    			        <tr>
    			            <td colspan="6"><strong>@lang('garage::lang.insurance_desc'):</strong>
    			            <br><p  style="text-align:left; ">
    			            {!!$job_sheet->insurance_desc!!}
    			            </p>
    			        </td>
    			        </tr>
    			
    			        
    			    </tr>
    
            </table>
        
    @endif
    
    
      
      	@if($job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both')
      	       <table class="table-pdf width-100">
                    <tr>
                        <th style="text-align:center;background-color:#d9d9d9;border-style: none" colspan="6">Cash info</th>
                    </tr>
    	
    				<tr>
    					<th style="width:16.5%"><strong>Amount :</strong></th>
    					<td style="width:16.5%">{{$job_sheet->cash_cost }}</td>
    					<th style="width:16.5%"><strong>Deposit:</strong></th>
    					<td style="width:16.5%">{{$job_sheet->deposit }}</td>				
    					<th style="width:16.5%"><strong>Balance :</strong></th>
    					<td style="width:16.5%">{{$job_sheet->cash_cost -  $job_sheet->deposit}}</td>
    
    			        <tr>
    			            <td colspan="6"><strong>@lang('garage::lang.cash_desc'):</strong>
    			            <br><p  style="text-align:left; ">
    			            {!!$job_sheet->cash_desc!!}
    			            </p>
    			        </td>
    			        </tr>
    			
    			        
    			    </tr>
    
            </table>
    
    @endif 	
    
    
    <div class="box mb-10 width-100 " style="text-align:center;">
         <p  style="font-weight:bold;font-size:14px;text-align:center;border: 1px solid;background-color: #c3c2c2;margin: 0;line-height: 2;">@lang("lang_v1.notes")</p>
    
    
    	  {!!$job_sheet->notes!!}
    
    </div>
    
    <div class="box mb-10 width-100 " style="text-align:center;">
         <p  style="font-weight:bold;font-size:14px;text-align:center;border: 1px solid;background-color: #c3c2c2;margin: 0;line-height: 2;">@lang("lang_v1.terms_conditions")</p>
    
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

</div>


<table class="table-pdf">
	<tr>
		<th>
			@lang('garage::lang.customer_signature'):
		</th>
		
	</tr>
</table>

<footer>
 <img src="/public/uploads/img/Footer.png"> 
</footer>