<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\GarageCompanyController@update', [$status->id]), 'method' => 'put', 'id' => 'status_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'repair::lang.edit_status' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
          
           <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('name', __( 'garage::lang.company_name' ) . ':*') !!}
                  {!! Form::text('name', $status->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.company_name' ) ]); !!}
                </div>
            </div>
          <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('phone', __( 'garage::lang.phone' ) . ':*') !!}
                  {!! Form::text('phone', $status->phone, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.phone' ) ]); !!}
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('address', __( 'garage::lang.address' ) . ':*') !!}
                  {!! Form::text('address', $status->address, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.address' ) ]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('trn_no', __( 'garage::lang.trn_no' ) . ':*') !!}
                  {!! Form::text('trn_no', $status->trn_no, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.trn_no' ) ]); !!}
                </div>
            </div> 
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('to_email', __( 'garage::lang.to_email' ) . ':*') !!}
                  {!! Form::text('to_email', $status->to_email, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.to_email' ) ]); !!}
                </div>
            </div>
       
        </div>
    
    <div class="feature-tag-top-filds" id="feature-section2">
																@if(!empty($status->emails))
                                                                @php
                                                           $title =    explode(',', $status->emails);
                                                                    
                                                                   
                                                                    
                                                                @endphp
																	 @foreach($title as $key => $data1)

																<div class="feature-area">
																	<span class="remove feature-remove"><i class="fas fa-times"></i></span>
																	<div class="row">
																		<div class="col-lg-12 form-group">
																		<input type="text" name="emails[]" class="input-field form-control" placeholder="{{ __('emails') }}" value="{{ $title[$key] }}">
																		</div>

																	
																	</div>
                                                        
																</div>


																		@endforeach
																@else

																<div class="feature-area">
																	<span class="remove feature-remove"><i class="fas fa-times"></i></span>
																	<div class="row">
																		<div class="col-lg-12 form-group">
																		<input type="text" name="emails[]" class="input-field form-control" placeholder="{{ __('emails') }}" >
																		</div>

																	
																	</div>
                                  
																</div>

																@endif
															</div>

															<a href="javascript:;" id="feature-btn2" class="add-fild-btn"><i class="icofont-plus"></i> {{ __('Add More Emails') }}</a>
    
    
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->