<!-- Edit Order tax Modal -->
<div class="modal-dialog" role="document">
 
    
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"></h4>
		</div>
		<div class="modal-body">
		    
		       {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\LpoController@savelpo',$td->id),'files' => true,   'method' => 'post']  ) !!}
		
			
	          <input type="hidden" name="job_card_id" value="{{$td->id}}">
	          
		 	<div class="form-group">
			  	{!! Form::label('lpo_no', __( 'garage::lang.lpo_no' ) . ':') !!}
    		{!! Form::text('lpo_no', optional($td->lpo)->lpo_no ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.lpo_no' ), 'id' => 'lpo_no']); !!}
	
			</div>
			<div class="form-group">
			  	{!! Form::label('amount', __( 'garage::lang.amount' ) . ':') !!}
    		{!! Form::text('amount', optional($td->lpo)->amount ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.amount' ), 'id' => 'amount']); !!}
	
			</div>	
			<div class="form-group">
			  	{!! Form::label('excess', __( 'garage::lang.excesses' ) . ':') !!}
    		{!! Form::text('excess', optional($td->lpo)->excess ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.excesses' ), 'id' => 'excess']); !!}
	
			</div>
			<div class="form-group">
			  	{!! Form::label('tax_id', __( 'garage::lang.tax_id' ) . ':') !!}
    		{!! Form::select('tax_id',$taxes , optional($td->lpo)->tax_id ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.select' ), 'id' => 'tax_id']); !!}
	
			</div>
		 	<div class="form-group">
			  	{!! Form::label('claim_no', __( 'garage::lang.claim_no' ) . ':') !!}
    		{!! Form::text('claim_no', optional($td->lpo)->claim_no ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.claim_no' ), 'id' => 'claim_no']); !!}
	
			</div>
	<!--		<div class="form-group">
			  	{!! Form::label('trn_no', __( 'garage::lang.trn_no' ) . ':') !!}
    		{!! Form::text('trn_no', optional($td->lpo)->trn_no ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.trn_no' ), 'id' => 'trn_no']); !!}
	
			</div>-->
			<div class="form-group">
			  	{!! Form::label('lpo_date', __( 'garage::lang.lpo_date' ) . ':') !!}
    		{!! Form::text('lpo_date',   !empty(optional($td->lpo)->lpo_date)? @format_datetime(optional($td->lpo)->lpo_date) : null , ['class' => 'form-control', 'placeholder' => __( 'garage::lang.lpo_date' ), 'id' => 'update_date']); !!}
	
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