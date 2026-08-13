@extends('layouts.master')

@section('title')
    <title>Сервисные центры</title>
@endsection

@section('styles')
    <link rel="preload" href="{{ asset('assets/images/Loading_black.gif') }}" as="image">
    @vite(['resources/styles/pages/service-center/index.scss'])
@endsection

@php
    $f = [];
    for ($i = 0; $i < count($photos); $i++) {
        $f[] = rand(10, 100);
    }
    $i = 0;
@endphp

@section('content')
    @if ($coord)
        <input id="service_center_coord" type="hidden" name="service_center_coord" value="{{ json_encode($coord) }}" data-service-center-path={{ $serviceCenter->id }}>
    @endif
    @if (count($photos))
        @include('layouts.carousel.preview', ['photos' => $photos, 'modalPath' => 'carousel_photos', 'serviceCenter' => $serviceCenter])
    @endif
    @include('pages.service-center.layouts.heading', [
        'title' => $serviceCenter->name,
        'serviceCenter' => $serviceCenter,
    ])
    @include('pages.service-center.layouts.info', [
        'serviceCenter' => $serviceCenter,
        'web' => $web,
        'additionalPhones' => $additionalPhones,
    ])
    @include('pages.service-center.layouts.repair-specialization', ['equipmentTypes' => $equipmentTypes])
    @if ($serviceCenter->buyback && $buybackPrices->isNotEmpty())
        @include('pages.service-center.layouts.sell', ['equipmentTypes' => $equipmentTypes, 'buybackPrices' => $buybackPrices])
    @endif
    @if ($serviceCenter->average_rating !== null || $serviceCenter->reviewSources->isNotEmpty())
        @include('pages.service-center.layouts.feedback', ['serviceCenter' => $serviceCenter])
    @endif
@section('modal')
    @if (count($photos))
        @include('layouts.carousel.modal', ['photos' => $photos, 'modalTarget' => 'carousel_photos', 'serviceCenter' => $serviceCenter])
    @endif
@endsection
@endsection

@section('afterFooter')
<script src="https://api-maps.yandex.ru/2.1/?apikey=30c606be-6c96-48b4-a6a2-80eab6220ea3&lang=ru_RU"
    type="text/javascript"></script>
@vite(['resources/js/pages/service-center/index.js'])
@endsection
