@extends('layouts.app')
@section('title', 'LDM Home')
@section('content')
    <app-home :documents="{{ $documents }}"></app-home>
@endsection