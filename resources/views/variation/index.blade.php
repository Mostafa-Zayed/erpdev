@extends('layouts.app')
@section('title', __('product.variations'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('product.variations')
        <small>@lang('lang_v1.manage_product_variations')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_variations')])
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                data-href="{{action('VariationTemplateController@create')}}" 
                data-container=".variation_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="variation_table">
                <thead>
                    <tr>
                        <th>@lang('product.variations')</th>
                        <th>@lang('lang_v1.values')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade variation_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function(){

            /* render yajra datatables */
            let variation_table = $('#variation_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/variation-templates',
                columnDefs: [
                    {
                        targets: 2,
                        orderable: false,
                        searchable: false,
                    },
                ],
            });

            /* handle add variation value operation*/
            $(document).on('click', '#add_variation_values', function() {
                const html =
                    '<div class="form-group"><div class="col-sm-7 col-sm-offset-3"><input type="text" name="variation_values[]" class="form-control" required></div><div class="col-sm-2"><button type="button" class="btn btn-danger delete_variation_value">-</button></div></div>';

                $('#variation_values').append(html);
            });

            /* handle delete variation value operation*/
            $(document).on('click', '.delete_variation_value', function() {
                $(this).closest('.form-group').remove();
            });

            /* handle request of create new variation template */
            $(document).on('submit', 'form#variation_add_form', function(event) {
                event.preventDefault();
                let formElement = $(this);
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: formElement.serialize(),
                    beforeSend: function(xhr) {
                        __disable_submit_button(formElement.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success === true) {
                            $('div.variation_modal').modal('hide');
                            toastr.success(result.msg);
                            variation_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });

            /* handle request of edit variation template */
            $(document).on('click', 'button.edit_variation_button', function() {

                $('div.variation_modal').load($(this).data('href'), function() {

                    $(this).modal('show');

                    $('form#variation_edit_form').submit(function(event) {
                        event.preventDefault();
                        let formElement = $(this);
                        $.ajax({
                            method: 'POST',
                            url: $(this).attr('action'),
                            dataType: 'json',
                            data: formElement.serialize(),
                            beforeSend: function(xhr) {
                                __disable_submit_button(formElement.find('button[type="submit"]'));
                            },
                            success: function(result) {
                                if (result.success === true) {
                                    $('div.variation_modal').modal('hide');
                                    toastr.success(result.msg);
                                    variation_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    });
                });
            });

            $(document).on('click', 'button.delete_variation_button', function() {
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_variation,
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
                                    variation_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
        });
    </script>
@endsection


