@props(['previousURL' => false, 'nextURL' => false, 'position' => 1])

<nav role="pagination" aria-label="Page navigation">
    <div class="row justify-content-between align-items-center g-2 my-3">
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 d-grid">

            {{-- Previous Page Link --}}
            @if ($previousURL)
                <a href="{{ $previousURL }}" class="btn btn-outline-secondary" rel="prev">{!! __('pagination.previous') !!}</a>
            @else
                <button class="btn btn-outline-secondary" type="button" disabled>{!! __('pagination.previous') !!}</button>
            @endif

        </div>
        <div class="d-none d-md-block col-md-6 col-lg-8 text-center">
            <span>{{ __('main.page_position', ['position' => $position]) }}</span>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 d-grid">

            {{-- Next Page Link --}}
            @if ($nextURL)
                <a href="{{ $nextURL }}" class="btn btn-outline-secondary" rel="next">{!! __('pagination.next') !!}</a>
            @else
                <button class="btn btn-outline-secondary" type="button" disabled>{!! __('pagination.next') !!}</button>
            @endif

        </div>
    </div>
</nav>