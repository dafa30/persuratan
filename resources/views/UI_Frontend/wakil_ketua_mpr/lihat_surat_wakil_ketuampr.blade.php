@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
@php
    // Prioritas: $backUrl controller > ?back= (base64) > referer > halaman muka
    $backTo = $backUrl
        ?? (request()->filled('back') ? base64_decode(request('back'), true) : null)
        ?? request()->headers->get('referer')
        ?? route('halaman_muka');
@endphp
@include('surat.partials.lihat-body')
@endsection
