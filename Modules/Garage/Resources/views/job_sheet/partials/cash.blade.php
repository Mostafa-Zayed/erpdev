<!-- Edit Order tax Modal -->
<div class="modal-dialog" role="document">
 
    
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"></h4>
		</div>
		<div class="modal-body">
		    
		       {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\LpoController@savecash',$td->id),'files' => true,   'method' => 'post']  ) !!}
		
			
	          <input type="hidden" name="job_card_id" value="{{$td->id}}">
	          
		 
			<div class="form-group">
			  	{!! Form::label('amount', __( 'garage::lang.amount' ) . ':') !!}
    		{!! Form::text('amount', optional($td->cash_invoice)->total_before_tax ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.amount' ), 'id' => 'amount']); !!}
	
			</div>	
		
			<div class="form-group">
			  	{!! Form::label('tax_id', __( 'garage::lang.tax_id' ) . ':') !!}
    		{!! Form::select('tax_id',$taxes , optional($td->cash_invoice)->tax_id ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.select' ), 'id' => 'tax_id']); !!}
	
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