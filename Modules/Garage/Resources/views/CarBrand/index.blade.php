<button type="button" class="btn btn-sm btn-primary btn-modal pull-right" 
    data-href="{{action('\Modules\Garage\Http\Controllers\CarBrandController@create')}}" 
    data-container=".view_modal">
    <i class="fa fa-plus"></i>
    @lang( 'messages.add' )
</button>
<br><br>
<table class="table table-bordered table-striped" id="CarBrand" style="width: 100%">
    <thead>
    <tr>
        <th>@lang( 'garage::lang.brand_name' )</th> 
      
        <th>@lang( 'messages.action' )</th>
    </tr>
    </thead>
</table>
<div class="modal fade brands_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>