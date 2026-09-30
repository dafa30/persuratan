@extends('layouts.app')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    <h1>Selamat Datang di Sistem Surat</h1>
    <p>Sistem ini memungkinkan Anda untuk mengelola surat masuk dan keluar dengan mudah.</p>
    <a href="{{ route('surat.create') }}" class="btn btn-success mb-3">Tambah Surat Baru</a>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Surat Masuk</h5>
                    <p class="card-text">Lihat dan kelola surat masuk yang tersedia.</p>
                    <a href="{{ route('surat.masuk') }}" class="btn btn-primary">Lihat Surat Masuk</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Surat Keluar</h5>
                    <p class="card-text">Lihat dan kelola surat keluar yang tersedia.</p>
                    <a href="{{ route('surat.keluar') }}" class="btn btn-primary">Lihat Surat Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection