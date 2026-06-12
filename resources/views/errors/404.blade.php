@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan | Inventaris F&B')

@section('content')
    <!-- Teks Error 404 -->
    <div class="text-center py-5">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Halaman Tidak Ditemukan</p>
        <p class="text-gray-500 mb-0">Sepertinya halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
        <a href="{{ route('dashboard') }}" class="mt-4 d-inline-block">&larr; Kembali ke Beranda</a>
    </div>
@endsection
