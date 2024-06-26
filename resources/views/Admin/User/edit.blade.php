@extends('Admin.Layouts.master')
@section('page-title', 'User Management')

@section('main-content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">User Management</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboards</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.users')}}">User Management</a></li>
                            <li class="breadcrumb-item active">Update User</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        {{ Form::open(['route' => ['admin.users.update', ['id' => $user->id]], 'id' => 'userFrom', 'data-parsley-validate', 'files' => true, 'autocomplete' => 'off']) }}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Update User</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('error'))
                        <div class="alert alert-danger mb-2" role="alert">{{ Session::get('error') }}</div>
                        @endif

                        @if(Session::has('success'))
                        <div class="alert alert-success mb-2" role="alert">{{ Session::get('success') }}</div>
                        @endif

                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger mb-2" role="alert">{!! $errors->first() !!}</div>
                        @endforeach

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    {{Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => 'Enter name', 'id' => 'name', 'required'])}}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    {{Form::email('email', $user->email, ['class' => 'form-control', 'placeholder' => 'Enter email address', 'id' => 'email', 'required', 'data-parsley-email'=>"true", 'data-parsley-email-message'=>"Please provide a unique email"])}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    {{Form::select('role', $roles, @$user->roles->first()->id,['class' => 'form-select', 'required', 'placeholder' => 'Please select role'])}}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="password" class="form-label">New Password</label>
                                    {{Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter new password', 'id' => 'password'])}}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    {{Form::password('password_confirmation',['class' => 'form-control', 'placeholder' => 'Enter confirm password', 'id' => 'password_confirmation', 'data-parsley-equalto' => "#password", 'data-parsley-equalto-message' => "Confirm password should be the same as new password"])}}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    @php($status = array('Active' => 'Active', 'Inactive' => 'Inactive'))
                                    {{Form::select('status', $status, '',['class' => 'form-select', 'required'])}}
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
  
        $('#userFrom').submit(function() {
            $('.vertical-overlay').show(); 
        });

        window.ParsleyValidator.addValidator('email', 
            function (value) {
                var valid = false;
                $.ajax({
                    url: '{{route("admin.users.check-email")}}',
                    data: {
                        email: value,
                        id: {{$user->id}},
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