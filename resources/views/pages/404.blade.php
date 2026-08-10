@extends('layouts.master')

@section('title')
    <title>404 — Сервисные центры</title>
@endsection

@section('styles')
    @vite(['resources/styles/pages/404/index.scss'])
@endsection

@section('content')
    <section class="undefined container">
        <h2 class="undefined_title">Что-то пошло не так</h2>
        <h2 class="undefined_title">404</h2>
    </section>
@endsection

@section('modal')
@endsection

@section('afterFooter')
@endsection
