<x-layouts.app>
    @push('metaData')
        {!! $metaData->render() !!}
    @endpush
    <x-slot:pageTitle>{{ $pageTitle }}</x-slot>

    <div class="container">
        <div class="row g-2 justify-content-center">
            <div class="col-12 col-sm-10 col-md-8">

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 bg-white border border-1 rounded-2 px-3 py-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('index') }}">{{ __('main.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="tw-line-clamp-1">{{ $pageTitle }}</span>
                        </li>
                    </ol>
                </nav>
                <!-- [END] Breadcrumb -->

                <div class="card">
                    <div class="card-body">
                        <h1 class="fs-2 mb-3">{{ $pageTitle }}</h1>
                        <div>
                            {!! $pageContent !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>