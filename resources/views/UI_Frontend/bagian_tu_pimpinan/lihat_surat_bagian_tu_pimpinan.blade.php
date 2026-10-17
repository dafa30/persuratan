@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
@php
    $backTo = match ($kategori) {
        'biasa'   => route('surat.bagianTU_biasa'),
        default   => route('surat.index'),
    };
@endphp
@include('surat.partials.lihat-body')
@endsection
