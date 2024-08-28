@extends('layouts.main')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/adminlte/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/showLoadingSpinner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/toastify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
@endpush

@push('jscript')
    <script src="{{ asset('js/plugins/jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/plugins/showLoadingSpinner.js') }}"></script>
    <script src="{{ asset('js/plugins/showToastify.js') }}"></script>
    <script src="{{ asset('js/pages/login.js') }}"></script>
@endpush

@section('content')
<section class="content d-flex">

    <div class="container-fluid">
        <div class="row h-100">
            <div class="col-md-12 d-flex justify-content-center align-items-center h-100">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title">LOGIN DASHBOARD</h2>
                    </div>
                    <form id="formLogin" autocomplete="off">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" id="email" placeholder="email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="with-icon">
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                    <i class="pass_hide nav-icon fa fa-eye opacity-07"></i>
                                    <i class="pass_show nav-icon fa fa-eye-slash opacity-07"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" class="btn btn-primary">login</button>
                        </div>
                        <div class="text-center mt-3 pb-4">
                            <a href="" class="text-primary text-underline" data-toggle="modal" data-target="#modal-forgot-pass">lupa password ?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-forgot-pass">
        <div class="modal-dialog">
            <form id="formForgotPass" class="modal-content" autocomplete="off">
                <div class="modal-header">
                    <h4 class="modal-title">Lupa Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" autocomplete="off" style="position: relative;">
                            <div class="form-group mb-4">
                                <label for="email"><small><b>Email</b></small></label>
                                <input type="text" class="form-control" id="email" name="email">
                                <span id="email-error" class="invalid-feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Kirim</button>
                </div>
            </form>
        </div>
    </div>

</section>
@endsection
