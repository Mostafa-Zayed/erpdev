<!-- Edit Order tax Modal -->
<div class="modal-dialog" role="document">
 
    
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"></h4>
		</div>
		<div class="modal-body">
		    
		       {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\GarageSettingsController@saveimage'),'files' => true,   'method' => 'post']  ) !!}
		
			
			  <input type="hidden" name="jobcard_id" value="{{$td->id}}">
			  	<div class="form-group">
			    <label>@lang('garage::lang.stamp')</label>
		    	{!! Form::file('stamp', null,  ['class' => 'form-control','required','style' => 'width:100%']); !!}
			</div>
			   	<div class="form-group">
			    <label>@lang('garage::lang.signature')</label>
		    	{!! Form::file('signature', null,  ['class' => 'form-control','required','style' => 'width:100%']); !!}
			</div>
			 	<div class="form-group">
			    <label>@lang('garage::lang.garage_invoice_footer')</label>
		    	{!! Form::textarea('garage_invoice_footer', $td->garage_invoice_footer,  ['class' => 'form-control','id' => 'garage_invoice_footer','style' => 'width:100%']); !!}
			</div>
			  
			  <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
				{!! Form::close() !!}
		</div>	
	
		<div class="modal-footer">
		    <button type="button" class="btn btn-default" data-dismiss="modal">
		    	@lang('messages.close')
		    
		    </button>

		  
		</div>
	</div><!-- /.modal-content -->
	
	
	
</div><!-- /.modal-dialog -->

<script type="text/javascript">
	$('input#invoice_url').click(function(){
		$(this).select().focus();
	});
</script>