@extends('layouts.app-copy')

@section('title', __('garage::lang.edit_job_sheet'))

@section('content')
@include('garage::layouts.nav')
<style>
   .btn-file{
       height: 40px;
   } 
/*@media only screen and (max-width: 767px){*/
/*   #car-diagram{*/
/*       width: 100% !important;*/
/*       height: auto !important;*/
/*   }*/
/*}*/
</style>
<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/theme.css?ver=1.0">
<!--<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/simcify.min.css?ver=1.0">
--><link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/css/asilify.css?ver=1.0">
<link rel="stylesheet" href="https://asilify.dottedcraft.com/assets/libs/summernote/summernote-lite.min.css?ver=1.0">
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('garage::lang.job_sheet')
        (<code>{{$job_sheet->job_sheet_no}}</code>)
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
    {!! Form::open(['url' => action('\Modules\Garage\Http\Controllers\JobSheetController@update', ['id' => $job_sheet->id]), 'method' => 'put', 'id' => 'edit_job_sheet_form', 'files' => true]) !!}
        @includeIf('garage::job_sheet.partials.scurity_modal')
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="job_sheet_id" value="{{$job_sheet->id}}">
                        <div class="form-group hide">
                            {!! Form::label('contact_id', __('role.customer') .':*') !!}
                            <div class="input-group ">
                                <input type="hidden" id="default_customer_id"
                                value="{{ $job_sheet->customer->id }}" >
                                <input type="hidden" id="default_customer_name" value="{{ $job_sheet->customer->name }}" >
                                <input type="hidden" id="default_customer_balance" value="{{$job_sheet->customer->balance}}" >

                                {!! Form::select('contact_id', 
                                    [], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required', 'style' => 'width: 100%;']); !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""  @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                        
                           <div class="form-group col-md-6 show_new" >
                                {!! Form::label('name', __('garage::lang.customer_name') .' : ') !!}
                                
                                <span>{{$job_sheet->customer->name}}</span>

                                
                            </div>
                            <div class="form-group col-md-6 show_new" >
                                {!! Form::label('mobile', __('garage::lang.customer_mobile') .' : ') !!}
                                
                             <span>{{$job_sheet->customer->mobile}}</span>

                                
                            </div>
                    </div>
                    
                           <div class="row col-md-12">
                        
                          <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('car_status', __('garage::lang.car_status') . ':') !!}
                                {!! Form::select('car_status', ['in'=>'in','out'=>'out'], $job_sheet->car_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('car_brand', __('product.brand') . ':') !!}
                                {!! Form::select('car_brand', $brands, $job_sheet->car_brand, ['class' => 'form-control select2 required', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('care_model', __('garage::lang.car_model') . ':') !!}
                                {!! Form::text('care_model', $job_sheet->care_model, ['class' => 'form-control required', 'placeholder' => __('garage::lang.car_model'), 'required']); !!}               
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('car_plate', __('garage::lang.car_plate') . ':') !!}
                                 {!! Form::text('car_plate', $job_sheet->car_plate, ['class' => 'form-control required', 'placeholder' => __('garage::lang.car_plate'), 'required']); !!}                   
                             </div>
                        </div>
                        
                          <div class="col-md-4">
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
                               
                    </div>   
                    
                    
                   
                </div>
             
               
            </div>
            
              <section class="container">
        <div class="form-group">
                        <div class="car-diagram-holder">
                            <canvas id="car-diagram"></canvas></div>
                        <input type="hidden" name="car_diagram" value="" id="car_diagram">
                        <div class="signature-tools text-center" id="controls">
                           <div class="signature-tool-item with-picker">
                                    <div><div class="dent-scratch-color red active" color-label=".color-red" color-code="#ff0000"></div></div>
                                </div>
                                <div class="signature-tool-item with-picker">
                                    <div><div class="dent-scratch-color blue" color-label=".color-blue" color-code="#1418FF"></div></div>
                                </div>
                               <input id="stroke" name='stroke' type="hidden" value="red">
           
                             <input id="lineWidth" name='lineWidth' type="hidden" value="5">
                             
                             
                             
                             <input id="car_image" type="hidden" value="{{   asset('/uploads/job_card/' . $job_sheet->car_marks)}}">
                             
                             
                            <div class="signature-tool-item" id="undo">
                                undo
                                <div class="tool-icon tool-undo" ></div>
                            </div>
                            <div class="nk-divider divider mt-2 mb-2"></div>
                         <p class="form-note mb-1 selected-label color-red" style="">
                                    
                                    <em class="icon ni ni-circle-fill dia red" style="color:#ff0000; border-radius: 50%;
        width: 20px;
        height: 20px;
      
        padding: 0;background-color: #ff0000;"></em> <span class="text-muted" style="color:#ff0000;">Dents marking selected</span></p>
                                <p class="form-note mb-1 selected-label color-blue" style="display: none;">
                                    <em class="icon ni ni-circle-fill dia blue" style="color:#1418FF; border-radius: 50%;
        width: 20px;
        height: 20px;
   
        padding: 0;background-color: #1418FF;"></em> <span class="text-muted" style="color:#1418FF;">Scratch marking selected</span></p> </div>
                        <div id="toolbar">
         
            <button class="hide" id="clear">Clear</button>
        </div>
                    </div>
                       <div class="col-sm-12" style="text-align: center;">
                    <div class="nk-divider divider mt-0 mb-1"></div>
                    <p>Mark for dents and scratches. Use <span style="color:#ff0000;"><em class="icon ni ni-circle-fill"></em> Red color</span> for dents and <span style="color:#1418FF;"><em class="icon ni ni-circle-fill"></em> Blue color</span> for scratches. This can not be updated once saved.</p>
                </div>
    </section>
        </div>
        
        
          
        
     
     
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <!--             <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('date_in', __('garage::lang.date_in') . ':') !!}
                            @show_tooltip(__('garage::lang.date_in_tooltip'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('date_in',  !empty($job_sheet->date_in)? @format_datetime($job_sheet->date_in) : null, ['class' => 'form-control', 'id' => 'date_in', 'readonly']); !!}
                                <span class="input-group-addon">
                                    <i class="fas fa-times-circle cursor-pointer clear_delivery_date2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                -->
                         @if($job_sheet->pay_types == 'cash' || $job_sheet->pay_types == 'both')
                    @php
                        $cash = true;
                    @endphp
                @else
                    @php
                        $cash = false;
                    @endphp
                @endif
                 @if($job_sheet->pay_types == 'insurance' || $job_sheet->pay_types == 'both')
                    @php
                        $excess = true;
                    @endphp
                @else
                    @php
                        $excess = false;
                    @endphp
                @endif
                
                
                
                     <div class="col-md-4" @if(!$excess) style="display: none;" @endif>
                            <div class="form-group">
                                {!! Form::label('type', __('garage::lang.type') . ':') !!}
                                {!! Form::select('type', ['OD'=>'OD','TP'=>'TP','REC'=>'REC'], $job_sheet->type, ['class' => 'form-control select2 required', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    <div class="col-sm-4 hide">
                        <div class="form-group">
                            <label for="status_id">{{__('sale.status') . ':*'}}</label>
                            <select name="status_id" class="form-control status" id="status_id" required>
                            </select>
                        </div>
                    </div>
                   
                    
                       <div class="col-md-12 hide">
                        {!! Form::label('pay_types',  __('garage::lang.pay_type').':*', ['style' => 'margin-left:20px;'])!!}
                        <br>
                        <label class="radio-inline">
                            {!! Form::radio('pay_types', 'cash', $job_sheet->pay_types == 'cash' ? true : false, [ 'class' => 'input-icheck', 'required']); !!}
                            @lang('garage::lang.cash')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('pay_types', 'excess', $job_sheet->pay_types == 'insurance' ? true : false, [ 'class' => 'input-icheck']); !!}
                            @lang('garage::lang.excess')
                        </label>
                        <label class="radio-inline radio_btns">
                            {!! Form::radio('pay_types', 'both', $job_sheet->pay_types == 'both' ? true : false, [ 'class' => 'input-icheck']); !!}
                            @lang('garage::lang.both')
                        </label>
                    </div>
                
                
              
                
                
                  
                    <div class="col-md-6 " style="display: none;" >
                        <div class="form-group">
                            {!! Form::label('insurance_cost', __('garage::lang.insurance_cost') . ':') !!}
                            {!! Form::number('insurance_cost',$job_sheet->insurance_cost, ['class' => 'form-control ', 'id' => 'insurance_cost', 'placeholder' => __('garage::lang.insurance_cost'), 'rows' => 3]); !!}
                        </div>
                    </div> 
                    
                    <div class="col-md-6 excess_company" @if(!$excess) style="display: none;" @endif>
                        <div class="form-group">
                            {!! Form::label('insurance_company_id', __('garage::lang.insurance_company') . ':') !!}
                           {!! Form::select('insurance_company_id', $devices, $job_sheet->insurance_company_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
               
                       <div class="col-md-6 excess_company" @if(!$excess) style="display: none;" @endif>
                            <div class="form-group">
                                {!! Form::label('excess', __('garage::lang.excesses') . ':') !!}
                                {!! Form::select('excess', ['yes'=>'yes','no'=>'no'], $job_sheet->excess, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                    <div class="col-md-6 excess_desc" @if(!$excess) style="display: none;" @endif>
                        <div class="form-group">
                            {!! Form::label('insurance_desc', __('garage::lang.insurance_desc') . ':') !!}
                            {!! Form::textarea('insurance_desc',$job_sheet->insurance_desc, ['class' => 'form-control ', 'id' => 'insurance_desc', 'placeholder' => __('garage::lang.insurance_desc'), 'rows' => 3]); !!}
                        </div>
                    </div>
               
                
                  
                    <div class="clearfix"></div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('police_report', __('garage::lang.police_report') . ':') !!}
                            {!! Form::file('police_report[]', ['id' => 'police_report', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div>  
                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('id_photo', __('garage::lang.id_photo') . ':') !!}
                            {!! Form::file('id_photo[]', ['id' => 'id_photo', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div> 
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('d_license', __('garage::lang.d_license') . ':') !!}
                            {!! Form::file('d_license[]', ['id' => 'd_license', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div> 
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('v_license', __('garage::lang.v_license') . ':') !!}
                            {!! Form::file('v_license[]', ['id' => 'v_license', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div>
                 
                  
                    <hr>
                  
                  
           
                </div>
            </div>
        </div>
        
                   <div class="box box-solid pos-main-sec ct cash_cost" @if(!$cash) style="display: none;" @endif>
         <div class="row">
                     
                        
                      <div class="col-md-6 cash_cost" >
                            <div class="form-group">
                                {!! Form::label('cash_cost', __('garage::lang.cash_cost') . ':') !!}
                                {!! Form::number('cash_cost',$job_sheet->cash_cost, ['class' => 'form-control ', 'id' => 'cash_cost', 'placeholder' => __('garage::lang.cash_cost'), 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6 cash_desc" >
                            <div class="form-group">
                                {!! Form::label('cash_desc', __('garage::lang.cash_desc') . ':') !!}
                                {!! Form::textarea('cash_desc',$job_sheet->cash_desc, ['class' => 'form-control ', 'id' => 'cash_desc', 'placeholder' => __('garage::lang.cash_desc'), 'rows' => 3]); !!}
                            </div>
                        </div>
                     
                        <div class="col-md-6 cash_cost" >
                            <div class="form-group">
                                {!! Form::label('deposit', __('garage::lang.deposit') . ':') !!}
                                {!! Form::number('deposit',$job_sheet->deposit, ['class' => 'form-control ', 'id' => 'deposit', 'placeholder' => __('garage::lang.deposit'), 'rows' => 3]); !!}
                            </div>
                        </div>
                    </div>  
         </div>
        
         <div class="pos-main-sec ct ">
         <div class="row">
                      <div class="col-md-12" >
                            <div class="form-group">
                                {!! Form::label('notes', __('garage::lang.notes') . ':') !!}
                                {!! Form::textarea('notes',$job_sheet->notes, ['class' => 'form-control ', 'id' => 'notes', 'placeholder' => __('garage::lang.notes'), 'rows' => 6]); !!}
                            </div>
                        </div>
                   <div class="clearfix"></div>
                    <hr>
                         <div class="col-sm-12 text-left">
                    <input type="hidden" name="submit_type" id="submit_type">
                   <!-- <button type="submit" class="btn btn-success submit_button" value="save_and_add_parts">
                    @lang('garage::lang.save_and_add_parts')
                    </button>-->
                    <button type="submit" class="btn btn-primary submit_button" value="submit">
                        @lang('messages.save')
                    </button>
                  <!--  <button type="submit" class="btn btn-info submit_button" value="save_and_upload_docs">
                        @lang('garage::lang.save_and_upload_docs')
                    </button>-->
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
  
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

    </div>

</section>
@stop
@section('css')
    @include('garage::job_sheet.tagify_css')
@stop
@section('javascript')

<script>
           
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
           
                 
                });
            
            
            
            
              }); // end  
        
           
      
</script>
    <script type="text/javascript">
    var currency = "KSh";
   
    var carsketch =  $('#car_image').val();

</script>
            
<!--<script src="https://asilify.dottedcraft.com/assets/js/bundle.js?ver=1.0"></script>
--><script src="https://asilify.dottedcraft.com/assets/libs/jquery-ui/jquery-ui.min.js?ver=1.0"></script>
<!--<script src="https://asilify.dottedcraft.com/assets/js/scripts.js?ver=1.0"></script>-->
<script src="https://asilify.dottedcraft.com/assets/js/simcify.min.js?ver=1.0"></script>
<!--<script src="https://asilify.dottedcraft.com/assets/js/asilify.js?ver=1.0"></script>
--><script src="https://asilify.dottedcraft.com/assets/libs/jcanvas/jcanvas.min.js?ver=1.0"></script>
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
$("#car-diagram").attr({'width':$('.car-diagram-holder').width(), 'height':$('.car-diagram-holder').width()/2});

  const image = new Image();
        image.src = carsketch;
        image.onload = function() {
            ctx.drawImage(image, 0, 0, $('.car-diagram-holder').width(), $('.car-diagram-holder').width()/2);
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

<script>
     if ($('textarea#insurance_desc').length > 0) {
        tinymce.init({
            selector: 'textarea#insurance_desc',
            height:250
        });
    }  
     if ($('textarea#cash_desc').length > 0) {
        tinymce.init({
            selector: 'textarea#cash_desc',
            height:250
        });
    }  
    
       
  
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
      
      
        $('input[type=radio][name=pay_types]').on('ifChecked', function(){
              if ($(this).val() == 'cash') {
                $("div.cash_cost").show();
                $("div.cash_desc").show();
                
                  $("div.excess_cost").hide();
                $("div.excess_desc").hide();
                $("div.excess_company").hide();
                  $("input.insurance_cost").val(0);
                
              } else if($(this).val() == 'excess') {
                $("div.cash_cost").hide();
                $("div.cash_desc").hide();
                
                $("div.excess_cost").show();
                $("div.excess_desc").show();
                $("div.excess_company").show();
                $("input.cash_cost").val(0);
                
              } else{
                   $("div.cash_cost").show();
                $("div.cash_desc").show();  
                
                   $("div.excess_cost").show();
                $("div.excess_desc").show();
                $("div.excess_company").show();
                  
              }
            });
            
            
                  $("body").on("click", ".dent-scratch-color", function(event) {

    $("body").find(".dent-scratch-color").removeClass("active");
    $(this).addClass("active");

    if(modules.diagramcolor !== undefined){
        modules.diagramcolor($(this).attr("color-code"));
    }

    var selectedColor = $(this).attr("color-label");

    $("body").find(".selected-label").hide();
    $("body").find(selectedColor).show();

});
             $('#date_in').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $(document).on('click', '.clear_delivery_date2', function() {
                $('#date_in').data("DateTimePicker").clear();
            });
            
              //initialize file input
            $('#upload_job_sheet_image').fileinput({
                showUpload: true,
                showPreview: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            });

         $('#police_report').fileinput({
                showUpload: true,
                showPreview: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            });  
            
            $('#id_photo').fileinput({
                showUpload: true,
                showPreview: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            }); 
            $('#d_license').fileinput({
                showUpload: true,
                showPreview: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            }); 
            $('#v_license').fileinput({
                showUpload: true,
                showPreview: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove
            });

</script>

    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready( function() {
            $('.submit_button').click( function(){
                
                  var img = canvas.toDataURL("image/png");

                $('#car_diagram').val(img);
                
                $('#submit_type').val($(this).attr('value'));
            });
            $('form#edit_job_sheet_form').validate({
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

            @if(!empty($job_sheet->status_id))
                $("select#status_id").val({{$job_sheet->status_id}}).change();
            @elseif(!empty($default_status))
                $("select#status_id").val({{$default_status}}).change();
            @endif

            $('#delivery_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $(document).on('click', '.clear_delivery_date', function() {
                $('#delivery_date').data("DateTimePicker").clear();
            });

            var lock = new PatternLock("#pattern_container", {
                onDraw:function(pattern){
                    $('input#security_pattern').val(pattern);
                },
                enableSetPattern: true
            });

            @if(!empty($job_sheet->security_pattern))
                lock.setPattern("{{$job_sheet->security_pattern}}");
            @endif

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
                        job_sheet_id : $("#job_sheet_id").val()
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

            getModelRepairChecklists();

            $('input[type=radio][name=service_type]').on('ifChecked', function(){
              if ($(this).val() == 'pick_up' || $(this).val() == 'on_site') {
                $("div.pick_up_onsite_addr").show();
              } else {
                $("div.pick_up_onsite_addr").hide();
              }
            });

          
       

        });
    </script>
@endsection