@extends('Admin.Layouts.master')
@section('page-title', 'Logo Management')

@section('main-content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Logo Management</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboards</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.logo')}}">Logo Management</a></li>
                            <li class="breadcrumb-item active">Add Logo</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        {{ Form::open(['route' => 'admin.logo.store', 'id' => 'logoFrom', 'data-parsley-validate', 'files' => true, 'autocomplete' => 'off']) }}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Add New Logo</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('error'))
                        <div class="alert alert-danger mb-2" category="alert">{{ Session::get('error') }}</div>
                        @endif

                        @if(Session::has('success'))
                        <div class="alert alert-success mb-2" category="alert">{{ Session::get('success') }}</div>
                        @endif

                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger mb-2" category="alert">{!! $errors->first() !!}</div>
                        @endforeach

                        
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Enter Title', 'id' => 'title', 'required'])}}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    @php($status = array('Active' => 'Active', 'Inactive' => 'Inactive'))
                                    {{Form::select('status', $status, '',['class' => 'form-select', 'required'])}}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="display_order" class="form-label">Display Order<span class="text-danger">*</span></label>
                                    {{Form::number('display_order',$display_order, ['class' => 'form-control', 'placeholder' => 'Enter Display Order', 'id' => 'diamondrate','required', 'data-parsley-display_order'=>"true", 'data-parsley-display_order-message'=>"Display Order Number Already Taken"])}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                <label for="image" class="form-label">Image<span class="text-danger">*</span> </label>
                                    {{Form::file('image', ['class' => 'form-control', 'placeholder' => 'Select image', 'id' => 'image','required'])}}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                <label for="webp_image" class="form-label">Webp Image<span class="text-danger">*</span> </label>
                                    {{Form::file('webp_image', ['class' => 'form-control', 'placeholder' => 'Select Webp image', 'id' => 'webp_image','required'])}}
                                </div>
                            </div>
                        </div>
                           
                        <div class="row">
                            <div class="col-12">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                        
                </div>
            </div>
            <!--end col-->
        </div>
        {{ Form::close() }}

    </div>
    <!-- container-fluid -->
</div>
@endsection

@section('page-css')
@endsection

@section('page-js')
<script>
     $(function(){
      'use strict'

        $('#logoFrom').submit(function() {
            $('.vertical-overlay').show(); 
        });
  
        window.ParsleyValidator.addValidator('display_order', 
            function (value) {
                var valid = false;
                $.ajax({
                    url: '{{route("admin.logo.check-display-order")}}',
                    data: {
                        display_order: value,
                        _token: "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    dataType: "JSON",
                    async: false,
                    success: function(response) {
                        valid = response.valid;
                    }
                });
                return valid;
            },
        32);
  
    });

 </script>
@endsection 