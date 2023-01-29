<div class="mb-2">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">

            <a href="{{ route('index') }}" class="navbar-brand">{{ config('app.name') }}</a>

            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a 
                            href="{{ route('index') }}"
                            @class(['nav-link', 'active' => Route::currentRouteName() === 'index'])
                            @if (Route::currentRouteName() === 'index') aria-current="page" @endif
                        >{{ __('main.home') }}</a>
                    </li>
                </ul>

                @if (Route::currentRouteName() != 'index' && !empty(config('app.search_engine_id')))
                    <div class="mx-3 flex-fill d-none d-md-block">
                        <form action="https://cse.google.com/cse" method="GET">
                            <input type="hidden" name="cx" value="{{ config('app.search_engine_id') }}">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" placeholder="{{ __('main.search_placeholder') }}">
                                <button class="btn btn-primary" type="submit">{{ __('main.search') }}</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </nav>

    @if (Route::currentRouteName() != 'index' && !empty(config('app.search_engine_id')))
        <!-- Search bar mobile -->
        <div class="bg-dark d-block d-md-none">
            <div class="container">
                <form action="https://cse.google.com/cse" method="GET" class="pb-2">
                    <div class="input-group">
                        <input type="hidden" name="cx" value="{{ config('app.search_engine_id') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="{{ __('main.search_placeholder') }}">
                            <button class="btn btn-primary" type="submit">{{ __('main.search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- [END] Search bar mobile -->
    @endif
</div>