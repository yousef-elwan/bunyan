@extends('app.layouts.default')

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('website/home/css/hero-2.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/categories.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/featured-properties.css') }}">
    <link rel="stylesheet" href="{{ asset('website/home/css/advanced-search-modal.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        Object.assign(window.AppConfig.routes, {
            'search_page': "{{ route('search') }}"
        });
    </script>
@endpush


@php($metaData = $pages_meta['home'])
@section('og_title', $metaData['title'])
@section('twitter_title', $metaData['title'])
@section('description', $metaData['description'])
@section('og_description', $metaData['description'])
@section('twitter_description', $metaData['description'])
@section('keywords', $metaData['keywords'])


@section('content')
    <div class="" style="overflow-x: hidden">


        @include('app.pages.home._hero')

        @if (count($categories ?? []) > 0)
            @include('app.pages.home._categories')
        @endif
        {{-- @if (count($propertiesNew ?? []) > 0)
            @include('app.pages.home._list-properties', [
                'title' => __('app/home.lists.new_properties'),
                'allText' => __('common.view_all'),
                'allUrl' => route('search', ['sort' => 'newest_des']),
                'properties' => $propertiesNew,
            ])
        @endif --}}
        @if (count($propertiesNew ?? []) > 0)
            @include('app.pages.home._list-properties', [
                'title' => __('app/home.lists.new_properties'),
                'allText' => __('common.view_all'),
                'allUrl' => route('search', ['sort_by' => 'created_at', 'sort_dir' => 'desc']),

                'properties' => $propertiesNew,
            ])
        @endif
        @if (count($cities ?? []) > 0)
            @include('app.pages.home._cities')
        @endif

        @if (count($propertiesFeatures ?? []) > 0)
            @include('app.pages.home._list-properties', [
                'title' => __('app/home.lists.features_properties'),
                'allText' => __('common.view_all'),
                'allUrl' => route('search', ['sort' => 'featured_des']),
                'properties' => $propertiesFeatures,
            ])
        @endif


        @foreach ($categories ?? [] as $category)
            @if (count($category['properties']) > 0)
                @include('app.pages.home._list-properties', [
                    'title' => $category['name'],
                    'allText' => __('common.view_all'),
                    'allUrl' => route('search', ['category_id' => $category['id']]),
                    'properties' => $category['properties'],
                ])
            @endif
        @endforeach
    </div>
@endsection

@push('script')
    @vite(['resources/js/alpine/app/home/main.js'])
@endpush
