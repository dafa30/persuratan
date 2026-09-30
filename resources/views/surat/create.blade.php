@extends('layouts.app')
@section('content')
  @if (strtolower(optional(auth()->user()->role)->name ?? (auth()->user()->role ?? '')) === 'caraka')
    @include('surat.create_caraka')
  @else
    @include('surat.create_select')
  @endif
@endsection
