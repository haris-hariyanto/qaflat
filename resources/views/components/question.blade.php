<div class="card h-100">
    <div class="card-body">
        <!-- Question data -->
        <div class="d-flex mb-3 justify-content-between">
            <div>
                <a href="{{ route('subject', [Str::slug($question['subject'])]) }}">
                    <span class="small">{{ ucwords($question['subject']) }}</span>
                </a>
            </div>
            <div>
                <a href="{{ route('grade', [Str::slug($question['grade'])]) }}">
                    <span class="small">{{ ucwords($question['grade']) }}</span>
                </a>
            </div>
        </div>
        <!-- [END] Question data -->

        <!-- Question -->
        <div class="fw-semibold">
            <a href="{{ route('content', [$question['slug']]) }}" class="text-dark">
                <span class="tw-line-clamp-3">{{ $question['question'] }}</span>
            </a>
        </div>
        <!-- [END] Question -->
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small">{{ __('main.answers_count', ['count' => $question['answers_total']]) }}</span>
            </div>
            <div>
                <a href="{{ route('content', [$question['slug']]) }}" class="btn btn-primary">{{ __('main.answers') }}</a>
            </div>
        </div>
    </div>
</div>