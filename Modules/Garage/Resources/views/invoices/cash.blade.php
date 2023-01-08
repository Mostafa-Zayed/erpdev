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
    height:3000px
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
<div class="container">
  <!--  <div class="row">
        <div class="col-6">
            	@if(!empty(Session::get('business.logo')))
          <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 80px; ">
        @endif
        </div>
        <div class="col-6" style="text-align: center;">
             <p style="padding-top: 22px;">   {{optional($job_sheet->customer->business)->name}} <br> Tel:{{optional($job_sheet->customer->business->owner)->mobile}}<br>
              
            </p>
        </div>
    </div>-->
    
      <div class="row">
        <div class="col-6">
            	@if(!empty(Session::get('business.logo')))
          <img src="{{ asset( 'uploads/img/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
        @endif
        </div>
        <div class="col-6 text-center">
            <p style="padding-top: 22px;text-align: justify;margin-left: 60px;">
              	{{optional($job_sheet->businessLocation)->name}}<br>
          
			{{optional($job_sheet->businessLocation)->landmark}} ,  {{optional($job_sheet->businessLocation)->city}} , {{optional($job_sheet->businessLocation)->state}},{{optional($job_sheet->businessLocation)->country}} , {{optional($job_sheet->businessLocation)->zip_code}}
            <br>
                Tel:{{optional($job_sheet->businessLocation)->mobile}}
            </p>
        </div>
    </div>
    
    <!--<div class="row my-2" style="text-align: center;border-top: 1px solid #000;">
        

        @if(!empty(optional($job_sheet->customer->business->locations)))
        @foreach( $job_sheet->customer->business->locations->where('is_active',1) as $location)
        <div class="col-4" style="border-right: 1px solid;">
            <h5>{{$location->state}}</h5>
            <h5>{{$location->mobile}}</h5>
        </div>
        @endforeach
       <div class="col-4" style="border-right: 1px solid;">
             <h5>Sharjah</h5>
            <h5>054-4354111</h5>
        </div>
        <div class="col-4">
             <h5>Ajmon</h5>
            <h5>054-4354111</h5>
        </div>
      @endif  
        
        </div>
-->
<div class="text-center" style="background: paleturquoise;font-size:30px;font-weight:800;border-radius:50px;">
    TAX INVOICE
</div>
<div class="row my-3">
    <div class="col-12">
      <table style="width:100%;">
           <tr>
            <th rowspan="2" style="width:50%;" >
                <table style="width:100%;border-style: hidden;">
                    <tr >
                        <th>Mr./Mrs : {{$job_sheet->customer->name}} </th>
                    </tr>
                    <tr >
                        <th>Vechicle: {{optional($job_sheet->brand)->name}} / {{$job_sheet->care_model}}</th>
                    </tr>
                    <tr >
                        <th>Reg.No: {{$job_sheet->car_plate}}</th>
                    </tr>
                </table>
            </th>
            <td>TRN NO: {{optional($job_sheet->customer->business)->tax_number_1}}</td>
          </tr>
          <tr>
            <td>Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;invoice No:{{$job_sheet->job_sheet_no}}</td>
          </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table style="width:100%" class="text-center">
          <tr>
            <th style="width:10%;border-style: none;">SL.No</th>
            <th style="width:50%;border-style: none;">Particulars</th>
            <th style="width:10%;border-style: none;">unit</th>
            <th  style="width:10%">Amount</th>
          </tr>
          <tr>
            <td>1</td>
            <td>LABOUR CHARGES& SPare parts</td>
            <td>1</td>
            <td><span class="display_currency" data-currency_symbol="true">
			{{optional($job_sheet->cash_invoice)->total_before_tax ?? 0}}
		
			</span></td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
           <td style="text-align: right;">VAT <span class="display_currency" data-currency_symbol="false">
			{{optional($job_sheet->cash_invoice->tax)->amount ?? ''}}
		
			</span>%</td>
            <td colspan="2"><span class="display_currency" data-currency_symbol="true">
			{{optional($job_sheet->cash_invoice)->tax_amount ?? 0}}
		
			</span></td>
            
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td style="text-align: right;">Total</td>
            <td colspan="2"> <span class="display_currency" data-currency_symbol="true">
			{{optional($job_sheet->cash_invoice)->final_total ?? 0}}
		
			</span></td>
         
          </tr>
           <tr>
            <td colspan="2">
                <div class="row">
                <div class="col-6" style="text-align: left;">Dhs.</div>
                <div class="col-6" style="text-align: right;">Grand Total</div>
                </div>
                </td>
          
            <td colspan="2">&nbsp;</td>
            
          </tr>
        </table>
          <div class="row my-3">
        <div class="col-6">
        .................................<br>
        Reciver's Name&Sing<br>Received
        </div>
        <div class="col-6" style="text-align:right;">
            .............................<br>
            {{optional($job_sheet->customer->business)->name}} 
        </div>
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