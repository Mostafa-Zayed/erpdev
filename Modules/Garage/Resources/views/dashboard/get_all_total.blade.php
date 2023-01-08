@extends('layouts.app')

@section('title', __('garage::lang.job_sheets'))

@section('content')
@include('garage::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('garage::lang.job_sheets')
    </h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters'), 'closed' => false])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('contact_id',  __('role.customer') . ':') !!}
                {!! Form::select('contact_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        @if(in_array('service_staff' ,$enabled_modules) && !$is_user_service_staff)
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('technician',  __('garage::lang.technician') . ':') !!}
                    {!! Form::select('technician', $service_staffs, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        @endif
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status_id',  __('sale.status') . ':') !!}
                {!! Form::select('status_id', $status_dropdown['statuses'], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
           <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('car_plate',  __('garage::lang.car_plate') . ':') !!}
                        {!! Form::text('car_plate', null, ['class' => 'form-control', 'style' => 'width:100%']); !!}
                    </div>
                </div> 
    @endcomponent
	<div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#pending_job_sheet_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa-exclamation-circle text-orange"></i>
                            @lang('garage::lang.all_total')
                            @show_tooltip(__('garage::lang.common_pending_status_tooltip'))
                        </a>
                    </li>
                   
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pending_job_sheet_tab">
                  
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="pending_job_sheets_table">
                                <thead>
                                    <tr>
                                       <th>@lang('messages.action')</th>
                                        <th>@lang('messages.actions')</th> 
                                        <th>@lang('lang_v1.created_at')</th>
                                        <th>@lang('lang_v1.date_in')</th>
                                              <th>@lang('sale.status')</th>
                                         <th>@lang('garage::lang.car_status')</th>
                                         <th>@lang('garage::lang.repair_status')</th>
                                       <th>@lang('product.brand')</th> 
                                        <th>@lang('garage::lang.model')</th>
                                        <th>@lang('garage::lang.car_plate')</th>
                                        
                                        
                                        <th> @lang('garage::lang.cash_work')</th> 
                                        <th> @lang('garage::lang.claim_no')</th> 
                                        <th> @lang('garage::lang.lpo_no')</th> 
                                        <th> @lang('garage::lang.estimaition_amount')</th> 
                                        <th> @lang('garage::lang.lpo_amount')</th> 
                                        <th> @lang('garage::lang.excesses')</th> 
                                        <th> @lang('garage::lang.tax_amount')</th> 
                                        <th> @lang('garage::lang.deposit')</th> 
                                        <th> @lang('garage::lang.lpo_balance')</th> 
                                        <th> @lang('garage::lang.type')</th> 
                                        
                                        <th>
                                            @lang('garage::lang.pay_types')
                                        </th>
                                     
                                        <th>
                                            @lang('garage::lang.job_sheet_no')
                                        </th>
                                      
                                  
                                          <!--  <th>@lang('garage::lang.technician')</th>-->
                                     
                                        <th>
                                            @lang('role.customer')
                                        </th> 
                                        <th>
                                            @lang('role.mobile')
                                        </th>
                                        <th>@lang('business.location')</th>
                                   
                                        <th>@lang('garage::lang.insurance_company')</th>
                                       
                                        <!-- <th>@lang('garage::lang.serial_no')</th>-->
                                        <th>@lang('garage::lang.estimated_cost')</th>
                                         <th>
                                            @lang('garage::lang.expected_delivery_date')
                                        </th>
                                        <th>@lang('lang_v1.added_by')</th>
                                     
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            pending_job_sheets_datatable = $("#pending_job_sheets_table").DataTable({
                    processing: true,
                    serverSide: true,
                       bFilter: false,
                    ajax:{
                        url: '/garage/home/getallTotal/{{$status}}/{{$id}}',
                        "data": function ( d ) {
                            d.location_id = $('#location_id').val();
                            d.contact_id = $('#contact_id').val();
                            d.status_id = $('#status_id').val();
                            d.car_plate = $('#car_plate').val();
                            d.is_completed_status = 0;
                            @if(in_array('service_staff' ,$enabled_modules))
                                d.technician = $('#technician').val();
                            @endif
                        }
                    },
                    columnDefs: [{
                        targets: [0, 4],
                        orderable: false,
                        
                        searchable: false
                    }],
                    
                    aaSorting:[[2, 'desc']],
                    columns:[
                       { data: 'action', name: 'action' },
                        { data: 'actions', name: 'actions' },
                        { data: 'created_at', name: 'created_at'
                        }, 
                        { data: 'date_in', name: 'date_in'
                        },  
                        { data:'status', name: 'rs.name' },
                        { data:'car_status', name: 'car_status' },
                        { data:'re_status', name: 'rrs.name' },
                        { data: 'brand', name: 'b.name' }, 
                        { data: 'care_model', name: 'care_model' },
                        { data: 'car_plate', name: 'car_plate' },
                        { data: 'cash_work', name: 'cash_work'},
                        { data: 'claim_no', name: 'claim_no'},
                        { data: 'lpo_no', name: 'lpo_no'},
                        { data: 'amount', name: 'amount'},
                        { data: 'lpo_amount', name: 'lpo_amount'},
                        { data: 'excess', name: 'excess'},
                        { data: 'tax_amount', name: 'tax_amount'},
                        { data: 'deposit', name: 'deposit'},
                        { data: 'lpo_balance', name: 'lpo_balance'},
                        { data: 'type', name: 'type'},
                        
                        
                        
                        
                        
                        { data: 'pay_types', name: 'pay_types'},
                      
                        {
                            data: 'job_sheet_no', name: 'job_sheet_no'
                        },
                     
                     
                         /*   { data: 'technecian', name: 'technecian', searchable: false},*/
                     
                        { data: 'customer', name : 'contacts.name'},
                        { data: 'mobile', name : 'contacts.mobile'},
                        { data: 'location', name: 'bl.name' },
                      
                        { data: 'device', name: 'device.name' },
                      
                      
                        {
                            data: 'estimated_cost', name: 'estimated_cost'
                        },
                        { data: 'repair_days', name: 'repair_days', searchable: false},
                        { data: 'added_by', name: 'added_by', searchable: false},
                     
                    ],
                    "fnDrawCallback": function (oSettings) {
                        __currency_convert_recursively($('#pending_job_sheets_table'));
                    }
            });

      

            $(document).on('click', '#delete_job_sheet', function (e) {
                e.preventDefault();
                var url = $(this).data('href');
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        $.ajax({
                            method: 'DELETE',
                            url: url,
                            dataType: 'json',
                            success: function(result) {
                                if (result.success) {
                                    toastr.success(result.msg);
                                    pending_job_sheets_datatable.ajax.reload();
                                     
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            @if(auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))
                $(document).on('click', '.edit_job_sheet_status', function () {
                    var url = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'html',
                        success: function(result) {
                            $('#status_modal').html(result).modal('show');
                        }
                    });
                });
            @endif

            $('#status_modal').on('shown.bs.modal', function (e) {

                //initialize editor
                tinymce.init({
                    selector: 'textarea#email_body',
                });

                $('#send_sms').change(function() {
                    if ($(this). is(":checked")) {
                        $('div.sms_body').fadeIn();
                    } else {
                        $('div.sms_body').fadeOut();
                    }
                });

                $('#send_email').change(function() {
                    if ($(this). is(":checked")) {
                        $('div.email_template').fadeIn();
                    } else {
                        $('div.email_template').fadeOut();
                    }
                });

                if ($('#status_id_modal').length) {
                    ;
                    $("#sms_body").val($("#status_id_modal :selected").data('sms_template'));
                    $("#email_subject").val($("#status_id_modal :selected").data('email_subject'));
                    tinymce.activeEditor.setContent($("#status_id_modal :selected").data('email_body'));  
                }

                $('#status_id_modal').on('change', function() {
                    var sms_template = $(this).find(':selected').data('sms_template');
                    var email_subject = $(this).find(':selected').data('email_subject');
                    var email_body = $(this).find(':selected').data('email_body');

                    $("#sms_body").val(sms_template);
                    $("#email_subject").val(email_subject);
                    tinymce.activeEditor.setContent(email_body);

                    if ($('#status_modal .mark-as-complete-btn').length) {
                        if ($(this).find(':selected').data('is_completed_status') == 1) 
                        {
                            $('#status_modal').find('.mark-as-complete-btn').removeClass('hide');
                            $('#status_modal').find('.mark-as-incomplete-btn').addClass('hide');
                        } else {
                            $('#status_modal').find('.mark-as-complete-btn').addClass('hide');
                            $('#status_modal').find('.mark-as-incomplete-btn').removeClass('hide');
                        }
                    }
                });
            });
            
            $('#status_modal').on('hidden.bs.modal', function(){
                tinymce.remove("textarea#email_body");
            });
            
            $(document).on('click', '.update_status_button', function(){
                $('#status_form_redirect').val($(this).data('href'));
            })
            
            $(document).on('submit', 'form#update_status_form', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                var ladda = Ladda.create(document.querySelector('.ladda-button'));
                ladda.start();
                $.ajax({
                    method: $(this).attr("method"),
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result){
                        ladda.stop();
                        if(result.success == true){
                            $('#status_modal').modal('hide');
                            if (result.msg) {
                                toastr.success(result.msg);
                            }

                            if ($('#status_form_redirect').val()) {
                                window.location = $('#status_form_redirect').val();
                            }
                            pending_job_sheets_datatable.ajax.reload();
                             
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });   
            
            $(document).on('submit', 'form#add_photo', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                    var formData = new FormData();
                $.ajax({
                    method: $(this).attr("method"),
                    url: $(this).attr("action"),
                    dataType: "text",
                    data: data,
                    success: function(result){
                      
                        if(result.success == true){
                            $('.view_modal').modal('hide');
                        
                                toastr.success(result.msg);
                          

                            pending_job_sheets_datatable.ajax.reload();
                             
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change', '#location_id, #contact_id, #status_id,#car_plate, #technician',  function() {
                pending_job_sheets_datatable.ajax.reload();
                 
            });
        });
    </script>
@endsection