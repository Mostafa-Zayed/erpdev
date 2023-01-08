
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
body{
    min-height: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    height:3000;
}
body > .row{
    flex-grow: 1;
}
footer{
    flex-shrink: 0;
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

</style>
</head>


<body>
       <!-- Add currency related field-->
                <input type="hidden" id="__code" value="{{session('currency')['code']}}">
                <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
                <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
                <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
                <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
                <input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
                <input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
    <div class="container" style="width:100%">
    <div class="row">
        <div class="col-6">
            	@if(!empty(Session::get('business.logo')))
          <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
        @endif
        </div>
        <div class="col-6 text-center">
            <p>
             {{optional($job_sheet->businessLocation)->name}}<br>
          
			{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
            <br>
            {{optional($job_sheet->businessLocation)->mobile}}
            </p>
        </div>
    </div>
    <div class="text-center" style="font-weight: 900;font-size:22px;">
        TAX INVOICE
    </div>
    <br><br>
    <div class="row">
        <div class="col-6" >
          <table style="width:100%;text-align:center">
              <tr>
                <td style="width:20%">NAME</td>
                <td>{{optional($job_sheet->company)->name}}</td>
              </tr>
              <tr>
                <td style="width:20%">ADRESS</td>
                <td>{{optional($job_sheet->company)->address}}</td>
    
              </tr>
              <tr>
                <td style="width:20%">TEL</td>
                <td>{{optional($job_sheet->company)->phone}}</td>
              </tr>
              <tr>
                <td style="width:20%">TRN NO</td>
                <td>{{optional($job_sheet->company)->trn_no}}</td>
                
              </tr>
              <tr>
                <td style="width:20%">VEHICLE</td>
                <td>{{optional($job_sheet->brand)->name}} - {{$job_sheet->care_model}}</td>
                
              </tr>
              <tr>
                <td style="width:20%">REG NO</td>
                <td>{{$job_sheet->car_plate}}</td>
              </tr>
            </table>
        </div>
        <div class="col-6" >
           <table style="width:100%;text-align:center">
              <tr>
                <td style="width:48%">INVOICE NO</td>
                <td>{{$job_sheet->job_sheet_no}} </td>
              </tr>
              <tr>
                <td>INVOICE DATE</td>
                <td>	{{@format_datetime($job_sheet->created_at)}}</td>
    
              </tr>
              <tr>
                <td>LPO NO</td>
                <td>{{optional($job_sheet->lpo)->lpo_no ?? 'null'}}</td>
              </tr>
              <tr>
                <td>LPO DATE</td>
                <td>{{!empty(optional($job_sheet->lpo)->lpo_date)? @format_datetime(optional($job_sheet->lpo)->lpo_date) : 'null'}}</td>
                
              </tr>
              <tr>
                <td>CLAIM NO</td>
                <td>{{optional($job_sheet->lpo)->claim_no ?? 'null'}}</td>
                
              </tr>
              <tr>
                <td>TRN NO</td>
                <td>{{optional($job_sheet->customer->business)->tax_number_1}}</td>
              </tr>
            </table>
        </div>
    </div>
  
    <div class="row my-5" >
        <div class="col-12">
            <table style="width:100%">
              <tr >
                <th style="width:10%">SL NO</th>
                <th style="width:40%">DESCRIPTION</th>
                <th style="width:10%">UNIT</th>
                <th style="width:10%">UNIT PRICE</th>
                <th style="width:10%">AMOUNT</th>
              </tr>
              <tr>
                <td style="vertical-align: top;">1</td>
                <td style="height: 114px;vertical-align: top;">PARTS,DENTING,PAINTING WITH LABOUR CHARGES</td>
                <td style="vertical-align: top;">1</td>
                <td style="vertical-align: top;"> <span class="display_currency" data-currency_symbol="true">
		 {{optional($job_sheet->lpo)->amount ?? 0}}
		
			</span> </td>
                <td style="vertical-align: top;"><span class="display_currency" data-currency_symbol="true">
		 {{optional($job_sheet->lpo)->amount ?? 0}}
		
			</span></td>
              </tr>
           
              <tr>
                  <td style="border-bottom: 1px solid #0000;border-right: 1px solid #0000;"></td>
                  <th style="border-bottom: 1px solid #0000;" colspan="2"></th>
                  <td>EXCESS</td>
                  <td><span class="display_currency" data-currency_symbol="true">
	        	{{optional($job_sheet->lpo)->excess ?? 0}}
			</span> </td>
              </tr>
              <tr>
                  <td colspan="3" style="border-style: none;"></td>
                  <td>Subtotal</td>
                  <td><span class="display_currency" data-currency_symbol="true">
			{{optional($job_sheet->insurance_invoice)->total_before_tax ?? 0}}
		
			</span> </td>
                  
              </tr>
               <tr>
                  <td colspan="3" style="border-style: none;"></td>
                  <td>vat 5%</td>
                  <td><span class="display_currency" data-currency_symbol="true">
		    	{{optional($job_sheet->insurance_invoice)->tax_amount ?? 0}}
		
			</span> </td>
                  
              </tr>
              <tr>
                  <td colspan="3" style="border-style: none;"></td>
                  <td>Total</td>
                  <td><span class="display_currency" data-currency_symbol="true">
		
			{{optional($job_sheet->insurance_invoice)->final_total ?? 0}}
			</span> </td>
                  
              </tr>
            </table>
            <div class="text-center">
                <p>{!!optional($job_sheet->businessLocation)->garage_invoice_footer!!}</p>
            </div>
        </div>
    </div>
</div>
<footer>
    
    <img src="/public/uploads/img/Footer.png" style="width:100%;">
    
</footer>


        @include('layouts.partials.javascripts')
        <script>
            $(document).ready(function () {
            window.print();
        });

        </script>
</body>