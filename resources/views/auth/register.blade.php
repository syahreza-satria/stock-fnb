@extends('layouts.auth')

@section('title', 'Daftar Akun - Inventaris F&B')

@section('content')
<div class="card o-hidden border-0 shadow-lg my-5">
    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
            <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
            <div class="col-lg-7">
                <div class="p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Buat Akun Baru!</h1>
                    </div>
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="user" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" name="first_name" class="form-control form-control-user" id="exampleFirstName"
                                    placeholder="Nama Depan" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="last_name" class="form-control form-control-user" id="exampleLastName"
                                    placeholder="Nama Belakang" value="{{ old('last_name') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control form-control-user" id="exampleInputEmail"
                                placeholder="Alamat Email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="password" name="password" class="form-control form-control-user"
                                    id="exampleInputPassword" placeholder="Kata Sandi" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="password" name="password_confirmation" class="form-control form-control-user"
                                    id="exampleRepeatPassword" placeholder="Ulangi Kata Sandi" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Daftar Sekarang
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="#">Lupa Kata Sandi?</a>
                    </div>
                    <div class="text-center">
                        <a class="small" href="{{ route('login') }}">Sudah punya akun? Masuk!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection