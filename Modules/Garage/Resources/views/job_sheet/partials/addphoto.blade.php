<!-- Edit Order tax Modal -->
<div class="modal-dialog" role="document">
 
    
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"></h4>
		</div>
		<div class="modal-body">
		    
		       {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\JobSheetController@savephoto'),'files' => true,   'method' => 'post']  ) !!}
		
			
			  <input type="hidden" name="jobcard_id" value="{{$td->id}}">
			  	<div class="form-group">
			    <label>@lang('garage::lang.jobcard_photo')</label>
		    	{!! Form::file('job_card_photo', null,  ['class' => 'form-control','style' => 'width:100%']); !!}
			</div>
			  	<div class="form-group">
			    <label>@lang('garage::lang.estimation_photo')</label>
		    	{!! Form::file('estimation_photo', null,  ['class' => 'form-control','style' => 'width:100%']); !!}
			</div>
			  	<div class="form-group">
			    <label>@lang('garage::lang.lpo_photo')</label>
		    	{!! Form::file('lpo_photo', null,  ['class' => 'form-control','style' => 'width:100%']); !!}
			</div>
			  	<div class="form-group">
			    <label>@lang('garage::lang.receipt_photo')</label>
		    	{!! Form::file('receipt_photo', null,  ['class' => 'form-control','style' => 'width:100%']); !!}
			</div>
			  	<div class="form-group">
			    <label>@lang('garage::lang.invoice_photo')</label>
		    	{!! Form::file('invoice_photo', null,  ['class' => 'form-control','style' => 'width:100%']); !!}
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