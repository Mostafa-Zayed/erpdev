 <div class="box-tools">
                    <button type="button" class="btn btn-sm btn-primary btn-modal pull-right" 
                    data-href="{{action('TaxonomyController@create')}}?type=device" 
                    data-container=".category_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
<br><br>
 <table class="table table-bordered table-striped" id="category_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.device' )</th>
                         
                            <th>@lang( 'lang_v1.description' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
</table>

  <div class="modal fade category_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>