@extends('Admin.Layouts.master')
@section('page-title', 'pages Management')

@section('main-content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">pages Management</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboards</a></li>
                            <li class="breadcrumb-item active">pages Management</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h5 class="card-title mb-0 flex-grow-1">List all pages</h5>
                    </div>
                    <div class="card-body">

                        @if(Session::has('error'))
                        <div class="alert alert-danger mb-2" pages="alert">{{ Session::get('error') }}</div>
                        @endif

                        @if(Session::has('success'))
                        <div class="alert alert-success mb-2" pages="alert">{{ Session::get('success') }}</div>
                        @endif

                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger mb-2" pages="alert">{!! $errors->first() !!}</div>
                        @endforeach

                        <table id="pagesTable" class="display table table-bordered dt-responsive dataTable dtr-inline" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>

                    </div>
                </div>
            </div><!--end col-->
        </div>

    </div>
    <!-- container-fluid -->
</div>

@endsection

@section('page-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css?v='.env('FILE_VERSION'))}}" rel="stylesheet" type="text/css" />
@endsection

@section('page-js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js?v='.env('FILE_VERSION'))}}"></script>
<script>
    $(function(){
      'use strict'
        var oTable = $('#pagesTable').DataTable({
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "{{ route('admin.pages.ajax-list') }}",
            "aaSorting": [ [3,"desc"] ],
            "iDisplayLength": {{ show_per_page() }},
            "fnDrawCallback": function (oSettings) {
                feather.replace();
                $('.tooltipped').tooltip({ trigger: "hover" });
            },
            'aoColumns': [
                {'bSortable': false},
                {},
                {"class": 'text-center'},
                {"class": 'text-center'}
            ],
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: 'Show _MENU_ Items',
            }
        });

        $(document).on('click','.pages_active', function(){
            var idStr = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "This pages's user not able to login after this action!",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
                cancelButtonClass: "btn btn-danger w-xs mt-2",
                confirmButtonText: 'Yes, inactive pls!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: !1,
                showCloseButton: !0
            }).then(function(isConfirm) {
                if(isConfirm.value){
                    statusChange(oTable, idStr, 'Inactive');
                } else {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your action is safe :)',
                        icon:"error",
                        timer:2e3,
                        timerProgressBar: true,
                        showCancelButton: false,
                        buttonsStyling: !1,
                        showCloseButton: !0,
                        confirmButtonClass: "btn btn-primary mt-2"
                    });
                }
            });
        });

        $(document).on('click','.pages_inactive', function(){
            var idStr = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "This pages's user able to login after this action!",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
                cancelButtonClass: "btn btn-danger w-xs mt-2",
                confirmButtonText: 'Yes, active pls!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: !1,
                showCloseButton: !0
            }).then(function(isConfirm) {
                if(isConfirm.value){
                    statusChange(oTable, idStr, 'Active');
                } else {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your action is safe :)',
                        icon:"error",
                        timer:2e3,
                        timerProgressBar: true,
                        showCancelButton: false,
                        buttonsStyling: !1,
                        showCloseButton: !0,
                        confirmButtonClass: "btn btn-primary mt-2"
                    });
                }
            });
        });

    });

    function statusChange(oTable, id, val)
    {
        $('.vertical-overlay').show();
        setTimeout(() => {
            $.ajax({
                url: "{{ route('admin.pages.update-status') }}",
                data: {id: id, status: val, _token: '{{ csrf_token() }}'},
                type: "POST",
                success: function (result) {
                    $('.vertical-overlay').hide();
                    oTable.draw(false);
                },
                error: function(data){
                    $('.vertical-overlay').hide();
                }
            });
        }, 20);
    }
    
</script>
@endsection