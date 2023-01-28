<x-layouts.app>
    @push('metaData')
        {!! $metaData->render() !!}
        {!! $openGraph->render() !!}
    @endpush

    <x-slot:pageTitle>{{ $pageTitle }}</x-slot>

    <div class="container">
        <h1 class="h2 my-3">{{ __('main.subject_heading', ['subject' => $subjectModel->name]) }}</h1>

        <div class="row g-2">
            @foreach ($questions as $question)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-question :question="$question" />
                </div>
            @endforeach
        </div>

        <x-pagination :next-URL="$nextPageURL" :previous-URL="$prevPageURL" :position="$currentPage" />
    </div>
</x-layouts.app>