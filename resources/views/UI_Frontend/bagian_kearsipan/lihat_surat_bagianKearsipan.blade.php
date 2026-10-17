@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
@php
    $backTo = match ($kategori) {
        'biasa'          => route('surat.bagianKearsipan_biasa'),
        'rahasia'        => route('surat.bagianKearsipan_rahasia'),
        'sangat_rahasia' => route('surat.bagianKearsipan_sangat_rahasia'),
        default          => route('surat.index'),
    };
@endphp
@include('surat.partials.lihat-body')
@endsection
