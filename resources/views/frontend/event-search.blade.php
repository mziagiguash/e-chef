@extends('frontend.layouts.app')

@section('title', __('Event Search'))

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">{{ __('Search Events ') }}</h1>

            {{-- Language Switcher --}}
            <div class="text-center mb-4">
                <div class="btn-group" role="group">
                    @php
                        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
                        $currentLocale = request('lang', app()->getLocale());
                    @endphp

        @foreach($locales as $localeCode => $localeName)
            <a href="{{ route('event.search', ['locale' => $localeCode, 'search' => $search]) }}"
               class="btn btn-outline-primary {{ $localeCode === $currentLocale ? 'active' : '' }}">
                {{ $localeName }}
            </a>
        @endforeach
                </div>
            </div>

            {{-- Search Form --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('event.search', ['locale' => $currentLocale]) }}" method="GET">
    <div class="input-group">
        <input type="text" name="search" class="form-control form-control-lg"
               placeholder="{{ __('Search events...') }}" value="{{ $search }}">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search"></i> {{ __('Search') }}
        </button>
    </div>
</form>
                </div>
            </div>

            {{-- Results --}}
            @if($search)
                <div class="alert alert-info">
                    {{ __('Found :count events for ":search"', ['count' => $events->total(), 'search' => $search]) }}
                </div>
            @endif

            {{-- Events Grid --}}
            <div class="row">
                @forelse($events as $event)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($event->image)
                                <img src="{{ asset('uploads/events/' . $event->image) }}"
                                     class="card-img-top"
                                     alt="{{ $event->title }}"
                                     style="height: 200px; object-fit: cover;">
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>

                                @if($event->date)
                                    <p class="text-muted">
                                        <i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                    </p>
                                @endif

                                @if($event->location)
                                    <p class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $event->location }}
                                    </p>
                                @endif

                                @if($event->topic)
                                    <span class="badge bg-primary">{{ $event->topic }}</span>
                                @endif

                                @if($event->description)
                                    <p class="card-text mt-2">
                                        {{ Str::limit($event->description, 100) }}
                                    </p>
                                @endif
                            </div>

                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    {{ __('Hosted by') }}: {{ $event->hosted_by ?? __('Unknown') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            @if($search)
                                {{ __('No events found for your search.') }}
                            @else
                                {{ __('No events available.') }}
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($events->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endpush
