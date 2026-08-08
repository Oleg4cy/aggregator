@extends('layouts.master')

@section('title')
    <title>Agregator</title>
@endsection

@section('styles')
    <link rel="preload" href="{{ asset('assets/images/Loading_black.gif') }}" as="image">
    @vite(['resources/styles/pages/shop/index.scss'])
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
        <input id="shop_coord" type="hidden" name="shop_coord" value="{{ json_encode($coord) }}" data-shop-path={{ $shop->id }}>
    @endif
    @if (count($photos))
        @include('layouts.carousel.preview', ['photos' => $photos, 'modalPath' => 'carousel_photos'])
    @endif
    @include('pages.shop.layouts.heading', [
        'title' => $shop->name,
        'shop' => $shop,
    ])
    @include('pages.shop.layouts.info', [
        'shop' => $shop,
        'web' => $web,
        'additionalPhones' => $additionalPhones,
    ])
    @include('pages.shop.layouts.sell', ['categories' => $categories])
    @if ($shop->average_rating !== null || $shop->services->isNotEmpty())
        @include('pages.shop.layouts.feedback', ['shop' => $shop])
    @endif
    @if ($similars->isNotEmpty())
        @include('layouts.similar-companies', ['similars' => $similars])
    @endif
    @include('layouts.similar-categories-and-location', ['cityID' => $shop->city_id])
@section('modal')
    @if (count($photos))
        @include('layouts.carousel.modal', ['photos' => $photos, 'modalTarget' => 'carousel_photos'])
    @endif
@endsection
@endsection

@section('afterFooter')
<script src="https://api-maps.yandex.ru/2.1/?apikey=30c606be-6c96-48b4-a6a2-80eab6220ea3&lang=ru_RU"
    type="text/javascript"></script>
@vite(['resources/js/pages/shop/index.js'])
@endsection
