<style>
    .tables tr:nth-child(even) {
        background: transparent;
    }
    th{
        border: 0;
    }
    #pos_table thead th{
        color: #fff !important;
    }
    .total-tr td{
        color: #fff !important;
    }
    table .hover-q{
        right: -2px;
        top:-2px;
    }
</style>

<div class="row">
    <div class="table-responsive col-md-6 col-sm-6 col-12" style="overflow:auto;height:180px">
        <table class="table" style="background:#F99478;color:#fff;height:100%">
            <tr>
                <td>@lang('lang_v1.contact_id') : </td>
                <td> {{$customer->contact_id}}</td>
            </tr>
            <tr>
                <td>@lang('lang_v1.name') : </td>
                <td>{{$customer->name}} </td>
            </tr> 
            <tr>
                <td>@lang('lang_v1.email') : </td>
                <td> {{$customer->email}}</td>
            </tr> 
            <tr>
                <td> @lang('lang_v1.mobile') : </td>
                <td>{{$customer->mobile}}</td>
            </tr> 
            <tr>
                <td> @lang('garage::lang.id_number') : </td>
                <td>{{$customer->garage_id_number}}</td>
            </tr> 
            <tr>
              <td></td>
              <td><a href="{{ url('contacts',$customer->id)}}" target="_blank" class="btn btn-primary text-white">@lang('lang_v1.more_details') </a></td>
            </tr>
        </table>
    </div>          
				          
    <div class="table-responsive col-md-6 col-sm-6 col-12" style="overflow:auto;height:180px">
        <table class="table" style="background:#F5CD5B;color:#fff;height:100%">
            <thead>
                <tr>
                    <th>@lang('lang_v1.invoice_no')</th>
                    <th>@lang('lang_v1.date')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $log)
                    <tr>
                        <td><a href="#" data-href="{{action("SellController@show", [$log->id])}}" class="btn-modal" data-container=".view_modal" >{{$log->job_sheet_no}}</a></td>
                        <td>{{$log->created_at}}</td>
                    </tr> 
                @empty
                    <tr>
                        <td col="2">@lang('lang_v1.new Custmer dont have old Orders')</td>
                    </tr> 
                @endforelse
            </tbody>
        </table>
    </div>      
</div>

<script>
    // $(document).ready(function(){
    //      $('.ct .customer-info-table.show-hide-pos-form').click(function(){
    //         if($(this).siblings(".hidden-pos-form").is(":visible")){
    //             $(this).siblings('.hidden-pos-form').slideUp();
    //         } else{
    //             $(this).siblings('.hidden-pos-form').slideDown();
    //         }
    //     });    
    // });
</script>