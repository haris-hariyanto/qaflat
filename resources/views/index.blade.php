<x-layouts.app>
    <x-slot:pageTitle>{{ __('main.main_page_title') }}</x-slot>

    <div class="container">

        <!-- Hero -->
        <div class="card py-4">
            <div class="card-body">

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 col-xl-8">
                        <div>
                            <h1>{{ config('app.name') }}</h1>
                            <p>{{ __('main.main_subheading') }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- [END] Hero -->

        <!-- Latest question -->
        <h2 class="h3 my-3">{{ __('main.latest_questions') }}</h2>
        <div class="row g-2">
            @foreach ($questions as $question)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-question :question="$question" />
                </div>
            @endforeach
        </div>
        <!-- [END] Latest question -->

    </div>
</x-layouts.app>