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
}
body > .row{
    z-index: 9999;
    background: #fff;
    flex-grow: 1;
}
footer{
    flex-shrink: 0;
}
table, th, td {
  border: 3px solid black;
  border-collapse: collapse;
  height: 60px;
  font-size: larger;
}

td {
    
    font-weight: bold;
}
@media print{
 footer{
     position:fixed;
     bottom:0;
     }
}
</style>
</head>

<body>
    
<div class="row">
    <div class="col-12">
        <table style="width:100%" class="text-center">
          <tr>
            <th colspan="2" >JOB CARD</th>
         
          </tr> 
          
          <tr>
            <th style="width:10%;">ENTRY DATE</th>
            <td> {{$job_sheet->created_at}}</td>
          </tr>  
          
          <tr>
            <th style="width:10%;">VEHICLE</th>
            <td>{{optional($job_sheet->brand)->name}}&nbsp;&nbsp;  / &nbsp;&nbsp;{{$job_sheet->care_model}}</td>
          </tr> 
          <tr>
            <th style="width:10%;">PLATE NO</th>
            <td> {{$job_sheet->car_plate}}</td>
          </tr>
          <tr>
            <th style="width:10%;">INSURANCE / CASH</th>
            <td>{{optional($job_sheet->company)->name}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  {{$job_sheet->type}}</td>
          </tr>
         <tr>
            <th style="width:10%;">EXCESS</th>
            <td>{{$job_sheet->excess}}</td>
          </tr>
        <tr>
            <th style="width:10%;">CASH WORK</th>
            <td>{{$job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both' ?  'yes' : 'no' }}</td>
          </tr> 
          
        <!--  <tr>
            <th style="width:10%;">REPAIRING TIME</th>
            <td>{{$job_sheet->repair_days}} DAYS</td>
          </tr>-->
         <tr>
            <th style="width:10%;">cash detail</th>
            <td>{!!$job_sheet->cash_desc!!}</td>
          </tr>  
          
          <tr>
            <th style="width:10%;">Insurance detail</th>
            <td>{!!$job_sheet->insurance_desc!!}</td>
          </tr>  
          <tr>
            <th style="width:10%;">CONTACT NUMBER</th>
            <td>{{$job_sheet->customer->mobile}}</td>
          </tr>
          
       
        </table>
       
    </div>
</div>  
<br>
<footer>
    @lang('garage::lang.authorized_signature'):
    <br><br><br><br><br><br>
    <img src="/public/uploads/img/Footer.png" style="width: 100%"> 
</footer>


<script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

 <script>
            $(document).ready(function () {
            window.print();
        });

        </script>
</body>

