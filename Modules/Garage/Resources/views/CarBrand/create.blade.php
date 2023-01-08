<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\CarBrandController@store'), 'method' => 'post', 'id' => 'status_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'garage::lang.add_car_brand' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('name', __( 'garage::lang.brand_name' ) . ':*') !!}
                  {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'garage::lang.brand_name' ) ]); !!}
                </div>
            </div>
         
            
          
       
        </div>

   
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->