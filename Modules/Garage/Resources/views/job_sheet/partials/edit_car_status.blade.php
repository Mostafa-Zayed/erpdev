<div class="modal-dialog" role="document">
	<div class="modal-content">
		{!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\JobSheetController@updateCarStatus', [$job_sheet->id]), 'method' => 'put', 'id' => 'update_status_form']) !!}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang( 'garage::lang.edit_status' )</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<strong>
							@lang('garage::lang.job_sheet_no'):
						</strong>
						<span id="job_sheet_no">
							{{$job_sheet->job_sheet_no}}
						</span>
					</div>
				</div>
			<div class="row mt-15">
            	<div class="col-md-12">
            		<div class="form-group">
            	            {!! Form::label('car_status', __('garage::lang.car_status') . ':') !!}
                            {!! Form::select('car_status', ['in'=>'in','out'=>'out'], $job_sheet->car_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                    	</div>
            	</div>
            </div>
            <div class="row">
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label('update_note', __( 'garage::lang.update_note' ) . ':') !!}
				{!! Form::textarea('update_note', $status_update_data['update_note'] ?? null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.update_note' ), 'rows' => 3, 'id' => 'update_note' ]); !!}
			</div>
		</div>
		
		<div class="col-md-12">
		<div class="form-group">
			{!! Form::label('update_date', __( 'garage::lang.update_date' ) . ':') !!}
				{!! Form::text('update_date', !empty($job_sheet->date_in) ? @format_datetime($job_sheet->date_in) : null, ['class' => 'form-control', 'placeholder' => __( 'garage::lang.update_note' ), 'rows' => 3, 'id' => 'update_date' , 'readonly']); !!}
			</div>
		</div>
	</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="status_form_redirect">
				<button type="submit" class="btn btn-primary ladda-button update_status_button mark-as-incomplete-btn" data-href="">@lang( 'messages.update' )</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
			</div>

		{!! Form::close() !!}
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->