<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\GarageCompanyController@store'), 'method' => 'post', 'id' => 'status_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'garage::lang.add_status' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('name', __( 'garage::lang.company_name' ) . ':*') !!}
                  {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.company_name' ) ]); !!}
                </div>
            </div>
          <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('phone', __( 'garage::lang.phone' ) . ':*') !!}
                  {!! Form::text('phone', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.phone' ) ]); !!}
                </div>
            </div> 
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('address', __( 'garage::lang.address' ) . ':*') !!}
                  {!! Form::text('address', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.address' ) ]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('trn_no', __( 'garage::lang.trn_no' ) . ':*') !!}
                  {!! Form::text('trn_no', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.trn_no' ) ]); !!}
                </div>
            </div>
               <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('to_email', __( 'garage::lang.to_email' ) . ':*') !!}
                  {!! Form::text('to_email', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.to_email' ) ]); !!}
                </div>
            </div>
            
          
       
        </div>
   <div class="feature-tag-top-filds" id="feature-section2">
														
																<div class="feature-area">
																	<span class="remove feature-remove"><i class="fas fa-times"></i></span>
																	<div class="row">
																		<div class="col-lg-12 form-group">
																		<input type="text" name="emails[]" class="input-field form-control" placeholder="{{ __('garage::lang.emails') }}" >
																		</div>

																	
																	</div>
                                  
																</div>

															
															</div>

															<a href="javascript:;" id="feature-btn2" class="add-fild-btn"><i class="icofont-plus"></i> {{ __('Add More Emails') }}</a>
   
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->