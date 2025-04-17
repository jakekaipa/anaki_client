<div class="widget-box mb-30">
    <h4 class="widget-title">{{ __('Recent Posts') }}</h4>
    <div class="popular-widget-box">
        @foreach($recent_journals as $key => $item)
        <div class="single-popular-item d-flex flex-wrap align-items-center">
            <div class="popular-item-thumb">
                <a href="{{ route('web-journal.details', [$item->id, $item->slug]) }}"><img src="{{ get_image($item->image, 'web-jornal') }}" alt="blog"></a>
            </div>
            <div class="popular-item-content">
                <span class="date">{{ dateFormat('d F, Y', $item->created_at) }}</span>
                <h5 class="title"><a href="{{ route('web-journal.details', [$item->id, $item->slug]) }}">{{ $item->title->language->$defualt->title ? Str::limit($item->title->language->$defualt->title, 50, '...') : '' }}</a></h5>
            </div>
        </div>
        @endforeach
    </div>
</div>
