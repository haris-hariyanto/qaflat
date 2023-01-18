<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @stack('metaData')

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">


        <title>{!! isset($pageTitle) ? $pageTitle . ' - ' . config('app.name') : config('app.name') !!}</title>

        @vite(['resources/js/app.js'])
        @vite(['resources/css/tailwind.css'])

        @stack('scripts')
    </head>
    <body>
        @include('components.layouts.navbar')

        {{ $slot }}
    </body>
</html>