



@extends('layouts.app')

@section('title', __('garage::lang.add_job_sheet'))

@section('content')
@include('garage::layouts.nav')


<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/theme.css?ver=1.0">
<!--<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/simcify.min.css?ver=1.0">
--><link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/asilify.css?ver=1.0">
<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/libs/summernote/summernote-lite.min.css?ver=1.0">

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('garage::lang.job_sheet')
        <small>@lang('garage::lang.create')</small>
    </h1>
</section>
<section class="content">
    @if(!empty($repair_settings))
        @php
            $product_conf = isset($repair_settings['product_configuration']) ? explode(',', $repair_settings['product_configuration']) : [];

            $defects = isset($repair_settings['problem_reported_by_customer']) ? explode(',', $repair_settings['problem_reported_by_customer']) : [];

            $product_cond = isset($repair_settings['product_condition']) ? explode(',', $repair_settings['product_condition']) : [];
        @endphp
    @else
        @php
            $product_conf = [];
            $defects = [];
            $product_cond = [];
        @endphp
    @endif
    {!! Form::open(['action' => '\Modules\Garage\Http\Controllers\JobSheetController@store', 'id' => 'job_sheet_form', 'method' => 'post', 'files' => true]) !!}
        @includeIf('garage::job_sheet.partials.scurity_modal')
        
          <div id="customer_data" style="width:100% !important">
          </div>
          
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    @if(count($business_locations) == 1)
                        @php 
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php $default_location = null;
                        @endphp
                    @endif
                    <div class="col-md-3 @if(!empty($default_location)) hide @endif">
                        <div class="form-group">
                            {!! Form::label('location_id', __('business.business_location') . ':*' )!!}
                            {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('contact_id', __('role.customer') .':*') !!}
                            <div class="input-group">
                                <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] ?? ''}}" >
                                <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] ?? ''}}" >
                                <input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? ''}}" >

                                {!! Form::select('contact_id', 
                                    [], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required', 'style' => 'width: 100%;']); !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""  @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        {!! Form::label('service_type',  __('garage::lang.service_type').':*', ['style' => 'margin-left:20px;'])!!}
                        <br>
                        <label class="radio-inline">
                            {!! Form::radio('service_type', 'carry_in', false, [ 'class' => 'input-icheck', 'required']); !!}
                            @lang('garage::lang.carry_in')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('service_type', 'pick_up', false, [ 'class' => 'input-icheck']); !!}
                            @lang('garage::lang.pick_up')
                        </label>
                        <label class="radio-inline radio_btns">
                            {!! Form::radio('service_type', 'on_site', false, [ 'class' => 'input-icheck']); !!}
                            @lang('garage::lang.on_site')
                        </label>
                    </div>
                </div>
                <div class="row pick_up_onsite_addr" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pick_up_on_site_addr', __('garage::lang.pick_up_on_site_addr') . ':') !!}
                            {!! Form::textarea('pick_up_on_site_addr',null, ['class' => 'form-control ', 'id' => 'pick_up_on_site_addr', 'placeholder' => __('garage::lang.pick_up_on_site_addr'), 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('car_brand', __('product.brand') . ':') !!}
                            {!! Form::select('car_brand', $brands, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('care_model', __('garage::lang.car_model') . ':') !!}
                             {!! Form::text('care_model', null, ['class' => 'form-control', 'placeholder' => __('garage::lang.car_model'), 'required']); !!}               
                             </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('car_plate', __('garage::lang.car_plate') . ':') !!}
                             {!! Form::text('car_plate', null, ['class' => 'form-control', 'placeholder' => __('garage::lang.car_plate'), 'required']); !!}                   
                             </div>
                    </div>
                    
                    
                </div>
              
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('serial_no', __('garage::lang.serial') . ':*') !!}
                            {!! Form::text('serial_no', null, ['class' => 'form-control', 'placeholder' => __('garage::lang.serial'), 'required']); !!}
                        </div>
                    </div>
               <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('car_status', __('garage::lang.car_status') . ':') !!}
                            {!! Form::select('car_status', ['in','out'], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                        </div>
                    </div> 
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('type', __('garage::lang.type') . ':') !!}
                            {!! Form::select('type', ['OD','TP','REC'], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                        </div>
                    </div>
                </div>
              
            </div>
        </div>
        
        
         <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <section class="container">
        <div class="form-group">
                        <div class="car-diagram-holder">
                            <canvas id="car-diagram" style="width: 596px; height: 298px;" width="596" height="298"></canvas></div>
                        <input type="hidden" name="car_diagram" value="" id="car_diagram">
                        <div class="signature-tools text-center" id="controls">
                        <!--    <div class="signature-tool-item with-picker">
                                <div><div class="dent-scratch-color red" color-label=".color-red" color-code="#ff0000"></div></div>
                            </div>
                            <div class="signature-tool-item with-picker">
                                <div><div class="dent-scratch-color blue active" color-label=".color-blue" color-code="#1418FF"></div></div>
                            </div>-->
                               <input id="stroke" name='stroke' type="hidden" value="red">
           
                             <input id="lineWidth" name='lineWidth' type="hidden" value="5">
                             
                             
                            <div class="signature-tool-item" id="undo">
                                <div class="tool-icon tool-undo" ></div>
                            </div>
                            <div class="nk-divider divider mt-2 mb-2"></div>
                            <p class="form-note mb-1 selected-label color-red" style="display: none;"><em class="icon ni ni-circle-fill" style="color:#ff0000;"></em> <span class="text-muted">Dents marking selected</span></p>
                            <p class="form-note mb-1 selected-label color-blue" style=""><em class="icon ni ni-circle-fill" style="color:#1418FF;"></em> <span class="text-muted">Scratch marking selected</span></p>
                        </div>
                        <div id="toolbar">
         
            <button class="hide" id="clear">Clear</button>
        </div>
                    </div>
    </section>
                    
       
                </div>
               
            </div>
        </div>
        
        
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
               <!--     @if(in_array('service_staff' ,$enabled_modules))
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('service_staff', __('garage::lang.assign_service_staff') . ':') !!}
                                {!! Form::select('service_staff', $technecians, null, ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff')]); !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('comment_by_ss', __('garage::lang.comment_by_ss') . ':') !!}
                            {!! Form::textarea('comment_by_ss', null, ['class' => 'form-control ', 'rows' => '3']); !!}
                        </div>
                    </div>-->
                    
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('estimated_cost', __('garage::lang.estimated_cost') . ':') !!}
                            {!! Form::text('estimated_cost', null, ['class' => 'form-control input_number', 'placeholder' => __('garage::lang.estimated_cost')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="status_id">{{__('sale.status') . ':*'}}</label>
                            <select name="status_id" class="form-control status" id="status_id" required>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('delivery_date', __('garage::lang.expected_delivery_date') . ':') !!}
                            @show_tooltip(__('garage::lang.delivery_date_tooltip'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('completed_on', null, ['class' => 'form-control', 'id' => 'delivery_date', 'readonly']); !!}
                                <span class="input-group-addon">
                                    <i class="fas fa-times-circle cursor-pointer clear_delivery_date"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('images', __('garage::lang.document') . ':') !!}
                            {!! Form::file('images[]', ['id' => 'upload_job_sheet_image', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div>
                  <!--  <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('garage::lang.send_notification')</label><br>
                            <div class="checkbox-inline">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="send_notification[]" value="sms">
                                    @lang('garage::lang.sms')
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="send_notification[]" value="email">
                                    @lang('business.email')
                                </label>
                            </div>
                        </div>
                    </div>-->
                    <div class="clearfix"></div>
                    <hr>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            @php
                                $custom_field_1_label = !empty($repair_settings['job_sheet_custom_field_1']) ? $repair_settings['job_sheet_custom_field_1'] : __('lang_v1.custom_field', ['number' => 1])
                            @endphp
                            {!! Form::label('custom_field_1', $custom_field_1_label . ':') !!}
                            {!! Form::text('custom_field_1', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_2_label = !empty($repair_settings['job_sheet_custom_field_2']) ? $repair_settings['job_sheet_custom_field_2'] : __('lang_v1.custom_field', ['number' => 2])
                        @endphp
                        {!! Form::label('custom_field_2', $custom_field_2_label . ':') !!}
                        {!! Form::text('custom_field_2', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_3_label = !empty($repair_settings['job_sheet_custom_field_3']) ? $repair_settings['job_sheet_custom_field_3'] : __('lang_v1.custom_field', ['number' => 3])
                        @endphp
                        {!! Form::label('custom_field_3', $custom_field_3_label . ':') !!}
                        {!! Form::text('custom_field_3', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_4_label = !empty($repair_settings['job_sheet_custom_field_4']) ? $repair_settings['job_sheet_custom_field_4'] : __('lang_v1.custom_field', ['number' => 4])
                        @endphp
                        {!! Form::label('custom_field_4', $custom_field_4_label . ':') !!}
                        {!! Form::text('custom_field_4', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_5_label = !empty($repair_settings['job_sheet_custom_field_5']) ? $repair_settings['job_sheet_custom_field_5'] : __('lang_v1.custom_field', ['number' => 5])
                        @endphp
                        {!! Form::label('custom_field_5', $custom_field_5_label . ':') !!}
                        {!! Form::text('custom_field_5', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-12 text-right">
                    <input type="hidden" name="submit_type" id="submit_type">
                    <button type="submit" class="btn btn-success submit_button" value="save_and_add_parts">
                    @lang('garage::lang.save_and_add_parts')
                    </button>
                    <button type="submit" class="btn btn-primary submit_button" value="submit">
                        @lang('messages.save')
                    </button>
                    <button type="submit" class="btn btn-info submit_button" value="save_and_upload_docs">
                        @lang('garage::lang.save_and_upload_docs')
                    </button>
                </div>
                </div>
                
            </div>
        </div>
        
        
    {!! Form::close() !!} <!-- /form close -->
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
         <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
    $form_id = 'quick_add_contact';
    }

 
  @endphp
    {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\ContactController@store'), 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
      <div class="row">

      <div class="col-md-6 contact_type_div">
        <div class="form-group">
            {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                {!! Form::select('type', $types, null , ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
            </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']); !!}
            </div>
        </div>
      </div> 
      <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('garage_id_number', __('garage::lang.id_number') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                {!! Form::text('garage_id_number', null, ['class' => 'form-control','placeholder' => __('garage::lang.id_number'), 'required']); !!}
            </div>
        </div>
      </div>
 
      <div class="col-md-12">
        <hr/>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                {!! Form::email('email', null, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-mobile"></i>
                </span>
                {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('alternate_number', null, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
            </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('landline', __('contact.landline') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
            </div>
        </div>
      </div>
     
 
      <div class="clearfix"></div>

    </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
  <script>
      
        $(document).ready(function(){
             $("Select[name='country']").change(function(){
        
              var id= $(this).val();
              var url = "{{ url ('/city/name/')}}";
              var token = $("input[name='_token']").val();
              $.ajax({
                  url: url,
                  method: 'POST',
                  data: {id:id, _token:token},
                  success: function(data) {
                     
                    
                    $("[name='city']").html('');
                    $("[name='city']").html(data.options);
                     
                  }
                });
              });
              
              
              $("Select[name='city']").change(function(){
                  
        
              var id= $(this).val();
              var url = "{{ url ('/state/name/')}}";
              var token = $("input[name='_token']").val();
              $.ajax({
                  url: url,
                  method: 'POST',
                  data: {id:id, _token:token},
                  success: function(data) {
                     
                    
                    $("[name='state']").html('');
                    $("[name='state']").html(data.options);
                     
                  }
                });
              });
        
           });
           
      
</script>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

    </div>
</section>
@stop

@section('javascript')

    <script type="text/javascript">
    var currency = "KSh";
    var carsketch = "data:image/jpeg;base64,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";



</script>
            
<!--<script src="https://asilify.dottedcraft.com/assets/js/bundle.js?ver=1.0"></script>
--><script src="https://asilify.dottedcraft.com/assets/libs/jquery-ui/jquery-ui.min.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/js/scripts.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/js/simcify.min.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/js/asilify.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/libs/jcanvas/jcanvas.min.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/libs/jcanvas/cardiagram.min.js?ver=1.0"></script>
<script src="https://asilify.dottedcraft.com/assets/libs/summernote/summernote-lite.min.js?ver=1.0"></script>
<script>
    
    
  
       
      
        
    
const canvas = document.getElementById('car-diagram');
const toolbar = document.getElementById('toolbar');
const ctx = canvas.getContext('2d');

/*
        var width = $(".car-diagram-holder").width();
        var height = parseInt(width / 2);
        $("#car-diagram").attr("width", width).width(width);
        $("#car-diagram").attr("height", height).height(height);

const canvasOffsetX = canvas.offsetLeft;
const canvasOffsetY = canvas.offsetTop;



canvas.width = window.innerWidth - canvasOffsetX;
canvas.height = window.innerHeight - canvasOffsetY;


console.log(canvasOffsetX);
console.log(canvasOffsetY);
console.log(window.innerWidth);
console.log(window.innerHeight);

console.log(canvas.width );
console.log(canvas.height );

*/
  const image = new Image();
        image.src = carsketch;
        image.onload = function() {
            ctx.drawImage(image, 0, 0, 596, 298);
            initMarking();
        }
        
        /*
let isPainting = false;
let lineWidth = 5;
let startX;
let startY;

toolbar.addEventListener('click', e => {
    if (e.target.id === 'clear') {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
});

toolbar.addEventListener('change', e => {
    if(e.target.id === 'stroke') {
        ctx.strokeStyle = 'red';
    }

    if(e.target.id === 'lineWidth') {
        lineWidth = e.target.value;
    }
    
});

const draw = (e) => {
    if(!isPainting) {
        return;
    }

    ctx.lineWidth = lineWidth;
    ctx.lineCap = 'round';

    ctx.lineTo(e.clientX - canvasOffsetX, e.clientY);
    ctx.stroke();
}

canvas.addEventListener('mousedown', (e) => {
    isPainting = true;
    startX = e.clientX;
    startY = e.clientY;
});

canvas.addEventListener('mouseup', e => {
    isPainting = false;
    ctx.stroke();
    ctx.beginPath();
});
*/
canvas.addEventListener('mousemove', draw);



</script>
    
    <script  type="text/javascript">
    
      
           $("Select[name='contact_id']").change(function(){
        
                   
                      var id= $(this).val();
                     
              
                     var url = "{{ url ('garage/customer/data')}}";
                     var token = $("input[name='_token']").val();
           
              $.ajax({
                  url: url,
                  method: 'POST',
                  data: {id:id, _token:token},
                  success: function(data) {
                     
                      $("#customer_data").html('');
                      $("#customer_data").html(data.options);
                   
                     
                
                     
                  },
                  failed: function(data) {
                successmessage = 'This Customer Not Exist';
                
                 $("#customer_data").html(successmessage);
            },
                 
                });
            
            
            
            
              }); // end  
                     
       $(document).on('click', '.add_new_customer', function() {
        $('#customer_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal')
            .find('input#name')
            .val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('customer')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });
      
      
      
      
        $(document).ready( function() {
            $('.submit_button').click( function(){
                var img = canvas.toDataURL("image/png");

                $('#car_diagram').val(img);
                    
                $('#submit_type').val($(this).attr('value'));
            });
            $('form#job_sheet_form').validate({
                errorPlacement: function(error, element) {
                    if (element.parent('.iradio_square-blue').length) {
                        error.insertAfter($(".radio_btns"));
                    } else if (element.hasClass('status')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            var data = [{
              id: "",
              text: '@lang("messages.please_select")',
              html: '@lang("messages.please_select")',
            }, 
            @foreach($repair_statuses as $repair_status)
                {
                id: {{$repair_status->id}},
                @if(!empty($repair_status->color))
                    text: '<i class="fa fa-circle" aria-hidden="true" style="color: {{$repair_status->color}};"></i> {{$repair_status->name}}',
                    title: '{{$repair_status->name}}'
                @else
                    text: "{{$repair_status->name}}"
                @endif
                },
            @endforeach
            ];

            $("select#status_id").select2({
              data: data,
              escapeMarkup: function(markup) {
                return markup;
              }
            });

            @if(!empty($default_status))
                $("select#status_id").val({{$default_status}}).change();
            @endif

            $('#delivery_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $(document).on('click', '.clear_delivery_date', function() {
                $('#delivery_date').data("DateTimePicker").clear();
            });

           
            //filter device model id based on brand & device
            $(document).on('change', '#brand_id', function() {
                getModelForDevice();
                getModelRepairChecklists();
            });

            // get models for particular device
            $(document).on('change', '#device_id', function() {
                getModelForDevice();
            });
            
            $(document).on('change', '#device_model_id', function() {
                getModelRepairChecklists();
            });
            
            function getModelForDevice() {
                var data = {
                    device_id : $("#device_id").val(),
                    brand_id: $("#brand_id").val()
                };

                $.ajax({
                    method: 'GET',
                    url: '/repair/get-device-models',
                    dataType: 'html',
                    data: data,
                    success: function(result) {
                        $('select#device_model_id').html(result);
                    }
                });
            }

            function getModelRepairChecklists() {
                console.log('here');
                var data = {
                        model_id : $("#device_model_id").val(),
                    };
                $.ajax({
                    method: 'GET',
                    url: '/repair/models-repair-checklist',
                    dataType: 'html',
                    data: data,
                    success: function(result) {
                        $(".append_checklists").html(result);
                    }
                });
            }

            $('input[type=radio][name=service_type]').on('ifChecked', function(){
              if ($(this).val() == 'pick_up' || $(this).val() == 'on_site') {
                $("div.pick_up_onsite_addr").show();
              } else {
                $("div.pick_up_onsite_addr").hide();
              }
            });

            //initialize file input
            $('#upload_job_sheet_image').fileinput({
                showUpload: false,
                showPreview: false,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            });

            //initialize tags input (tagify)
            var product_configuration = document.querySelector('textarea#product_configuration');
            tagify_pc = new Tagify(product_configuration, {
              whitelist: {!!json_encode($product_conf)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });

            var product_defects = document.querySelector('textarea#defects');
            tagify_pd = new Tagify(product_defects, {
              whitelist: {!!json_encode($defects)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });

            var product_condition = document.querySelector('textarea#product_condition');
            tagify_p_condition = new Tagify(product_condition, {
              whitelist: {!!json_encode($product_cond)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });
        });
    </script>
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
  
  
    <script  type="text/javascript">
      var lock = new PatternLock("#pattern_container", {
                onDraw:function(pattern){
                    $('input#security_pattern').val(pattern);
                },
                enableSetPattern: true
            });
            
            
         

            
    </script>
        

    
   
@endsection