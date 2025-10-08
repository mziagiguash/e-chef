@extends('frontend.layouts.app')

@section('title', __('My Courses'))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => $locale]) }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>{{ __('Home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('My Courses') }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">{{ __('My Courses') }}</h1>
                <a href="{{ route('frontend.courses', ['locale' => $locale]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('Browse More Courses') }}
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($courses->count() > 0)
                <div class="row g-4">
                    @foreach($courses as $course)
                        @php
                            $courseTranslation = $course->translations->where('locale', $locale)->first();
                            $courseTitle = $courseTranslation->title ?? $course->translations->first()->title ?? $course->title ?? __('No Title');
                            $courseDescription = $courseTranslation->description ?? $course->translations->first()->description ?? $course->description ?? '';
                            $instructorTranslation = $course->instructor ? $course->instructor->translations->where('locale', $locale)->first() : null;
                            $instructorName = $instructorTranslation->name ?? $course->instructor->translations->first()->name ?? $course->instructor->name ?? __('No Instructor');
                        @endphp

                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                @if($course->thumbnail_image)
                                    <img src="{{ $course->thumbnail_image }}" class="card-img-top" alt="{{ $courseTitle }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-book fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ Str::limit($courseTitle, 50) }}</h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ Str::limit($courseDescription, 100) }}
                                    </p>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-user-tie me-1"></i>{{ $instructorName }}
                                        </small>
                                    </div>

                                    {{-- Прогресс --}}
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">{{ __('Progress') }}</small>
                                            <small class="fw-bold">{{ $course->progress }}%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success"
                                                 style="width: {{ $course->progress }}%"></div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $course->completed_lessons }}/{{ $course->total_lessons }} {{ __('lessons') }}
                                        </small>
                                    </div>

                                    <div class="mt-auto">
                                        @if($course->progress == 100)
                                            <div class="d-grid">
                                                <span class="btn btn-success btn-sm">
                                                    <i class="fas fa-check me-1"></i>{{ __('Completed') }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="d-grid">
                                                <a href="{{ route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]) }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-play me-1"></i>
                                                    {{ $course->progress > 0 ? __('Continue') : __('Start Learning') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">{{ __('No Courses Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('You haven\'t enrolled in any courses yet.') }}</p>
                    <a href="{{ route('frontend.courses', ['locale' => $locale]) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>{{ __('Browse Courses') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
