<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<style type="text/css">
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
    .parent {
        /*display: flex !important;*/
        /*align-items: center;*/
        /*justify-content: space-between;*/
        /*width: 100%;*/
        /*background: #f00;*/
    }
	.box{
		border: 0px solid;
	}
		.boxb {
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
    <div class="width-100 " style="margin:10px;">
    
    	<div  align="left" style="width:65%;background-color:#ced2d5;float:left;padding:10px">
    		<p style="text-align:left; font-weight: bold;">
    			<br>
    			
    			{{optional($job_sheet->businessLocation)->name}}
    			<br>
    		
    			{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
    
    			<br>
    			Mobile:{{optional($job_sheet->businessLocation)->mobile}}
    
    			
    		</p>
    	</div>
    	<div  align="right" style="width:25%;background-color: #ced2d5;padding:20px">
    		@if(!empty(Session::get('business.logo')))
              <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
            @endif
    	</div>
    </div>
    
    
    
    
    <div class="width-100 box mb-10" style="font-size:16px;">
       <p style="font-weight: bold;font-size:22px;text-align:center;" >Car Repair Estimate</p>
       <div class="parent">
            <span style="float: left"><span style="font-weight: bold;">@lang('receipt.date'):&nbsp;</span>{{@format_date($job_sheet->created_at)}}</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span style="float: right"><span style="font-weight: bold;">No: &nbsp;</span> {{$job_sheet->job_sheet_no}}</span>
       </div>
         <p><span style="font-weight: bold">Dear Insurance Company: &nbsp;</span>{{optional($job_sheet->company)->name}} </p>
       
       <p><span style="font-weight: bold">After assessing that car:&nbsp;</span>{{optional($job_sheet->brand)->name}}&nbsp;{{$job_sheet->care_model}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       <span style="font-weight: bold">@lang('garage::lang.car_plate'):&nbsp;</span>{{$job_sheet->car_plate}}</p>
       <p><span style="font-weight: bold">The car repair cost including the parts:&nbsp;</span>{!!$job_sheet->amount!!}@lang('garage::lang.aed')</p>
       <p><span style="font-weight: bold">Repairing Days:&nbsp;</span>{!!$job_sheet->repair_days!!}&nbsp; @lang('garage::lang.days')</p>
    
    
    </div>
    
    <div class="box mb-10" style="text-align: center;">
    <div class="width-100 content-div">
    	<div class="width-100">
    	 <div class="box-header with-border">
    		  <h3 class="box-title">{{ __('garage::lang.preview') }}:&nbsp;</h3>
    	 </div>
    		  <!-- /.box-header -->
    		  <img src="{{   asset('/uploads/job_card/' . $job_sheet->car_marks)}}" style="width: 50%;">
    		  <p><span style="color:#ff0000;"><em class="icon ni ni-circle-fill"></em> Red color</span> for accident and <span style="color:#1418FF;"><em class="icon ni ni-circle-fill"></em> Blue color</span> for dents.</p>
    	</div>
    
    </div>
    
    
    </div>
    
    
    
    
    
      
    
        <div class="boxb">
            <table class="table-pdf">
                <tr style="background-color: #9d936f;">
                    <th>Repairs</th>
                    <th>Car Parts</th> 
                </tr>
                <tr style="background-color: #cdcccc;">
                    <td>  {!!$job_sheet->comment_by_ss!!}  </td>
                    <td> {!!$job_sheet->parts_desc!!}</td>
               </tr>
               
            </table>
        </div>
    
    
    <table class="table-pdf"  style="border: 0!important;">
    	<tr >
    		
    		<th style="border-style: none;">
    	        <img src="{{   asset('/uploads/location/' . optional($job_sheet->businessLocation)->signature)}}" style="width: 100px;">
    		</th>
    		
    		 
    		 <th style="border: 0!important;"> <img src="{{   asset('/uploads/location/' . optional($job_sheet->businessLocation)->stamp)}}" style="width:200px;height:100px"></th>
    		
    	</tr>
    	<tr>
    	    
    	    <td style="border-style: none;">@lang('garage::lang.authorized_signature'):</td>
    	    </tr>
    </table>
    <br><br><br><br><br><br>
</div>
<footer>
<img src="/public/uploads/img/Footer.png" style="width: 100%">     
</footer>

