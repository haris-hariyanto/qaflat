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
            </div>

        </div>
    </nav>
</div>