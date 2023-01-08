@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
@include('garage::layouts.nav')

<style>
    .nav-tabs-custom>.tab-content {
        padding: 25px 10px;
    }
    .input-group-addon{
        display: flex;
        align-items: center;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fas fa-tools"></i>
        @lang('messages.settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        @php
           $cat_code_enabled = isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code'] ? false : true;
      
           $cat_code_enabled = true;
        @endphp
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @can('garage_status.access')
                    <li class="active">
                        <a href="#repair_status_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fa fas fa-check-circle"></i>
                            @lang('sale.status')
                            @show_tooltip(__('repair::lang.all_js_status_tooltip'))
                        </a>
                    </li>
                    @endcan
                    <li   @if (!auth()->user()->can('garage_status.access')) class="active" @endif  >
                        <a href="#repair_device_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-desktop"></i>
                            @lang('garage::lang.ins_company')
                           
                        </a>
                    </li>
                    <li>
                        <a href="#repair_device_models_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-bolt"></i>
                            @lang('garage::lang.car_brand')
                        
                        </a>
                    </li> 
                    <li>
                        <a href="#locations" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-bolt"></i>
                            @lang('business.business_locations')
                        
                        </a>
                    </li>
                    
                    <li>
                        <a href="#repair_settings_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fa fas fa-cogs"></i>
                            @lang('garage::lang.repair_settings')
                        </a>
                    </li>
                    
                </ul>
                <div class="tab-content">
                     @can('garage_status.access')
                    <div class="tab-pane active" id="repair_status_tab"> 
                        @includeIf('garage::status.index')
                    </div>  
                     @endcan
                    <div class="tab-pane" id="locations"> 
                        @includeIf('garage::locations.locations')
                    </div>
                    <!-- Device (Taxonomy)-->
                    <input type="hidden" name="category_type" id="category_type" value="device">
                    <div class="tab-pane taxonomy_body @if (!auth()->user()->can('garage_status.access')) active @endif " id="repair_device_tab">
                           @includeIf('garage::company.index')
                    </div>
                    <!-- /Device (Taxonomy)-->
                    <div class="tab-pane" id="repair_device_models_tab">
                   <!--     <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('brand_id',  __('product.brand') . ':') !!}
                                    {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('device_id',  __('repair::lang.device') . ':') !!}
                                    {!! Form::select('device_id', $devices, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        </div>-->
                        @includeIf('garage::CarBrand.index')
                    </div>
                    <div class="tab-pane" id="repair_settings_tab"> 
                        @includeIf('garage::settings.partials.repair_settings_tab')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            if ( target == '#repair_settings_tab') {
                //Repair Settings Tab Code
                $('#search_product').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: '/purchases/get_products?check_enable_stock=false',
                            dataType: 'json',
                            data: {
                                term: request.term,
                            },
                            success: function(data) {
                                response(
                                    $.map(data, function(v, i) {
                                        if (v.variation_id) {
                                            return { label: v.text, value: v.variation_id };
                                        }
                                        return false;
                                    })
                                );
                            },
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        $('#default_product')
                            .val(ui.item.value);
                        event.preventDefault();
                        $('#selected_default_product').text(ui.item.label);
                        $(this).val(ui.item.label);
                    },
                    focus: function(event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                    },
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

                $("select#repair_status_id").select2({
                  data: data,
                  escapeMarkup: function(markup) {
                    return markup;
                  }
                });

                @if(!empty($repair_settings['default_status']))
                    $("select#repair_status_id").val({{$repair_settings['default_status']}}).change();
                @endif

                if ($('#repair_tc_condition').length) {
                    tinymce.init({
                        selector: 'textarea#repair_tc_condition',
                    });
                }  
                if ($('#repair_tc_condition_ar').length) {
                    tinymce.init({
                        selector: 'textarea#repair_tc_condition_ar',
                    });
                } 
                if ($('#email_body').length) {
                    tinymce.init({
                        selector: 'textarea#email_body',
                    });
                }
            }
        });
        //Repair Status Tab Code
        var status_table = $('#status_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{action('\Modules\Garage\Http\Controllers\GarageStatusController@index')}}",
                aaSorting: [[3, 'asc']],
                columnDefs: [ {
                    "targets": 4,
                    "orderable": false,
                    "searchable": false
                } ]
            });
            
        var company_table = $('#company_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{action('\Modules\Garage\Http\Controllers\GarageCompanyController@index')}}",
                aaSorting: [[2, 'desc']],
                columnDefs: [ {
                    "targets": 4,
                    "orderable": false,
                    "searchable": false
                } ]
            });
    var CarBrand = $('#CarBrand').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{action('\Modules\Garage\Http\Controllers\CarBrandController@index')}}",
                       
                    },
                    columnDefs: [
                        {
                            targets: [0],
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    aaSorting: [[1, 'desc']],
                    columns: [   
                        { data: 'name', name: 'name' },
                        { data: 'action', name: 'action' },
                    
                     
                    ]
            });


 
  var  business_location = $('#business_location').DataTable({
        processing: true,
        serverSide: true,
        bPaginate: false,
        buttons: [],
        ajax: "{{action('\Modules\Garage\Http\Controllers\GarageSettingsController@locations')}}",
        columnDefs: [
            {
                targets: 2,
                orderable: false,
                searchable: false,
            },
        ],
    });


        $(document).on('submit', 'form#status_form', function(e){
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        status_table.ajax.reload();
                        company_table.ajax.reload();
                        CarBrand.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
        $(document).on('shown.bs.modal', '.view_modal', function() {   
            
            if ($('textarea#garage_invoice_footer').length > 0) {
        tinymce.init({
            selector: 'textarea#garage_invoice_footer',
            height:250
        });
    }  
            $('input#color').colorpicker({format: 'hex'});
            
            
          
    
        })
        //Repair Device Model Code
        model_datatable = $("#model_table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{action('\Modules\Garage\Http\Controllers\CarBrandController@index')}}",
                        data:function(d) {
                            d.brand_id = $("#brand_id").val();
                            d.device_id = $("#device_id").val();
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0],
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    aaSorting: [[1, 'desc']],
                    columns: [
                        { data: 'action', name: 'action' },
                        { data: 'name', name: 'name' },
                     
                    ]
            });
            
            

        $(document).on('change', "#brand_id, #device_id", function(){
            model_datatable.ajax.reload();
        });

        $(document).on('click', '#add_device_model', function () {
            var url = $(this).data('href');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#device_model_modal').html(result).modal('show');
                }
            });
        });

        $(document).on('click', '.edit_device_model', function () {
            var url = $(this).data('href');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#device_model_modal').html(result).modal('show');
                }
            });
        });

        $('#device_model_modal').on('show.bs.modal', function (event) {
            $('form#device_model').validate();
            $("form#device_model .select2").select2();
        });

        $(document).on('submit', 'form#device_model', function(e){
            e.preventDefault();
            var url = $('form#device_model').attr('action');
            var method = $('form#device_model').attr('method');
            var data = $('form#device_model').serialize();
            $.ajax({
                method: method,
                dataType: "json",
                url: url,
                data:data,
                success: function(result){
                    if (result.success) {
                        $('#device_model_modal').modal("hide");
                        toastr.success(result.msg);
                        model_datatable.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('click', '#delete_a_model', function(e) {
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
                                model_datatable.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>


<script type="text/javascript">
    $(document).ready( function() {
        //Category table
        if ($('#category_table').length) {
            var category_type = $('#category_type').val();
            category_table = $('#category_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/taxonomies?type=' + category_type,
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
            });
        }
    });
    $(document).on('submit', 'form#category_add_form', function(e) {
        e.preventDefault();
        $(this)
            .find('button[type="submit"]')
            .attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div.category_modal').modal('hide');
                    toastr.success(result.msg);
                    category_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', 'button.edit_category_button', function() {
        $('div.category_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#category_edit_form').submit(function(e) {
                e.preventDefault();
                $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            $('div.category_modal').modal('hide');
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });

    $(document).on('click', 'button.delete_category_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_category,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    
    
    
     $(document).on('shown.bs.modal', '.view_modal', function() {
            
            
            
        
    $("#feature-btn2").on('click', function(){

$("#feature-section2").append(''+
                        '<div class="feature-area">'+
                            '<span class="remove feature-remove"><i class="fas fa-times"></i></span>'+
                                '<div  class="row">'+
                                    '<div class="col-lg-12 form-group">'+
                                        '<input type="text" name="emails[]" class="input-field form-control" placeholder="emails">'+
                                    '</div>'+
                                
                                    '</div>'+
                               
                                '</div>'+
                        '</div>'
                        +'');

});

$(document).on('click','.feature-remove', function(){

$(this.parentNode).remove();
if (isEmpty($('#feature-section2'))) {

$("#feature-section2").append(''+
                        '<div class="feature-area">'+
                            '<span class="remove feature-remove"><i class="fas fa-times"></i></span>'+
                            '<div  class="row">'+
                                    '<div class="col-lg-12 form-group">'+
                                        '<input type="text" name="emails[]" class="input-field form-control" placeholder="emails">'+
                                    '</div>'+
                                
                                    '</div>'+
                               
                                '</div>'+
                        '</div>'
                        +'');
$('.cp').colorpicker();
}

});
        
            
        })
    
    
</script>
@includeIf('taxonomy.taxonomies_js')
@endsection