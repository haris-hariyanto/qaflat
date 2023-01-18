<x-layouts.app>
    <x-slot:pageTitle>{{ $pageTitle }}</x-slot>

    <div class="container">
        <div class="row g-2">
            <!-- Main column -->
            <div class="col-12 col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 bg-white border border-1 rounded-2 px-3 py-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('index') }}">{{ __('main.home') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('subject', [Str::slug($content['subject'])]) }}">{{ ucwords($content['subject']) }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="tw-line-clamp-1">{{ $pageTitle }}</span>
                        </li>
                    </ol>
                </nav>
                <!-- [END] Breadcrumb -->

                <!-- Question -->
                <div class="card mb-2">
                    <div class="card-body">
                        <!-- Question data -->
                        <div class="mb-2 d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-dark btn-sm rounded-pill mb-1">
                                <span class="small">{{ ucwords($content['subject']) }}</span>
                            </a>
                            <a href="#" class="btn btn-outline-dark btn-sm rounded-pill mb-1">
                                <span class="small">{{ $content['grade'] }}</span>
                            </a>
                        </div>
                        <!-- [END] Question data -->

                        <h1 class="fs-4 lh-base mb-0">{!! nl2br(htmlspecialchars($content['question']), false) !!}</h1>
                    </div>
                </div>
                <!-- [END] Question -->

                <h2 class="fs-5 my-3">{{ __('main.answers') }}</h2>

                @foreach ($content['answers'] as $answer)
                    <div class="card mb-2" id="answer{{ $loop->iteration }}">
                        <div class="card-header">
                            <b>{{ __('main.answer_position', ['position' => $loop->iteration]) }}</b>
                        </div>
                        <div class="card-body">
                            {!! strip_tags(str_replace(['&lt;', '&gt;'], ['<', '>'], htmlspecialchars($answer['answer'])), config('content.allowed_tags')) !!}
                        </div>
                    </div>
                @endforeach

                <hr class="my-4">

                <h2 class="fs-5 mb-3">{{ __('main.related_questions') }}</h2>

                @foreach ($content['related_questions'] as $relatedQuestion)
                    <!-- Related question -->
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="h1 fs-6 lh-base mb-0">{!! nl2br(htmlspecialchars($relatedQuestion['question']), false) !!}</p>
                        </div>
                    </div>
                    <!-- [END] Related question -->

                    <!-- Related question answers -->
                    <div class="row g-0 justify-content-end">
                        <div class="col-10 col-lg-11">
                            <h3 class="fs-6 mb-2">{{ __('Answers') }}</h3>

                            @foreach ($relatedQuestion['answers'] as $relatedQuestionAnswer)
                                <div class="card mb-2">
                                    <div class="card-body">
                                        {!! strip_tags(str_replace(['&lt;', '&gt;'], ['<', '>'], htmlspecialchars($relatedQuestionAnswer['answer'])), config('content.allowed_tags')) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- [END] Related question answers -->
                @endforeach
            </div>
            <!-- [END] Main column -->

            <!-- Side column -->
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <b>{{ __('Other Questions') }}</b>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($content['internal_links'] as $internalLink)
                            <a href="{{ route('content', [$internalLink['slug']]) }}" class="list-group-item list-group-item-action">
                                <span class="tw-line-clamp-2">{{ strip_tags($internalLink['question']) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- [END] Side column -->
        </div>
    </div>
</x-layouts.app>