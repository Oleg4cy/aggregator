@extends('layouts.master')

@section('title')
    <title>Сервисные центры</title>
@endsection

@section('links_scripts')
@endsection

@section('styles')
    @vite(['resources/styles/pages/home/index.scss'])
@endsection

@section('content')
    @include('pages.home.layouts.hero')
    @include('pages.home.layouts.filter', ['serviceCenters' => $serviceCenters])
    @include('layouts.similar-equipment-types-and-location', ['cityID' => $cityID])
    @include('pages.home.layouts.mobile-nav')
@endsection

@section('modal')
@endsection

@section('afterFooter')
    <script defer src="https://api-maps.yandex.ru/2.1/?apikey=30c606be-6c96-48b4-a6a2-80eab6220ea3&lang=ru_RU"
        type="text/javascript" crossorigin="anonymous"></script>
    <script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
    @vite(['resources/js/pages/home/index.js'])
@endsection
