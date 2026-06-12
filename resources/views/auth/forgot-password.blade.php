@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi - Inventaris F&B')

@section('content')
<!-- Outer Row -->
<div class="row justify-content-center">

    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2">Lupa Kata Sandi?</h1>
                                <p class="mb-4">Tidak masalah, hal itu bisa terjadi. Masukkan alamat email Anda di bawah ini dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda!</p>
                            </div>
                            <form class="user" method="POST" action="#">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user"
                                        id="exampleInputEmail" aria-describedby="emailHelp"
                                        placeholder="Masukkan Alamat Email..." required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Kirim Tautan Reset
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('register') }}">Buat Akun Baru!</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Sudah punya akun? Masuk!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection