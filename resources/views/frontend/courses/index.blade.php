@extends('frontend.layouts.app')
@section('title', __('Our Courses'))

@section('content')
<div class="courses-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold mb-3">{{ __('Our Courses') }}</h1>
                    <p class="lead mb-4">{{ __('Discover the perfect learning path for you') }}</p>
                    <div class="hero-stats">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">{{ $allCourses->total() }}+</h3>
                                    <small>{{ __('Available Courses') }}</small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">100+</h3>
                                    <small>{{ __('Hours of Content') }}</small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">500+</h3>
                                    <small>{{ __('Happy Students') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Language Switcher -->
    <section class="language-section py-3 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                        <span class="me-2">{{ __('Choose language:') }}</span>
                        <div class="btn-group btn-group-sm" role="group">
                            @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $code => $name)
                                <a href="{{ route('frontend.courses', ['locale' => $code]) }}"
                                   class="btn btn-outline-primary {{ $locale == $code ? 'active' : '' }}">
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Grid -->
    <section class="courses-grid-section py-5">
        <div class="container">
            @if($courses->count() > 0)
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2 class="section-title">{{ __('Browse Our Courses') }}</h2>
                        <p class="section-subtitle">{{ __('Find the perfect course for your learning journey') }}</p>
                    </div>
                </div>

                <div class="row g-4" id="courses-container">
                    @foreach($courses as $course)
                        @php
                            $courseTranslation = $course->translations->where('locale', $locale)->first();
                            $instructorTranslation = $course->instructor ? $course->instructor->translations->where('locale', $locale)->first() : null;

                            // Название и описание курса
                            $courseTitle = $courseTranslation->title ?? $course->translations->first()->title ?? $course->title ?? __('No Title');
                            $courseDescription = $courseTranslation->description ?? $course->translations->first()->description ?? $course->description ?? __('No Description');
                            $instructorName = $instructorTranslation->name ?? ($course->instructor ? $course->instructor->translations->first()->name ?? $course->instructor->name : __('No Instructor'));

                            // Изображение курса
                            $courseImage = $course->image && file_exists(public_path('uploads/courses/' . $course->image))
                                ? asset('uploads/courses/' . $course->image)
                                : asset('images/default-course.jpg');

                            // Изображение инструктора
                            $instructorImage = ($course->instructor && $course->instructor->image && file_exists(public_path('uploads/users/' . $course->instructor->image)))
                                ? asset('uploads/users/' . $course->instructor->image)
                                : asset('images/default-user.jpg');

                            // Кодируем ID курса
                            $encodedCourseId = base64_encode($course->id);
                        @endphp

                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="card course-card h-100 shadow-sm border-0">
                                <div class="course-image-container position-relative overflow-hidden">
                                    <img src="{{ $courseImage }}"
                                         class="course-image img-fluid w-100"
                                         alt="{{ $courseTitle }}"
                                         style="height: 200px; object-fit: cover;"
                                         onerror="this.src='{{ asset('images/default-course.jpg') }}'">
                                    <div class="course-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-opacity">
                                        <a href="{{ route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]) }}"
   class="btn btn-primary btn-sm rounded-pill px-3">
    <i class="fas fa-eye me-1"></i>
    {{ __('View Course') }}
</a>
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="course-title fw-bold mb-2 text-truncate" title="{{ $courseTitle }}">
                                        {{ $courseTitle }}
                                    </h5>

                                    <div class="course-instructor d-flex align-items-center mb-2">
                                        <img src="{{ $instructorImage }}"
                                             alt="{{ $instructorName }}"
                                             class="rounded-circle me-2" width="28" height="28"
                                             style="object-fit: cover;"
                                             onerror="this.src='{{ asset('images/default-user.jpg') }}'">
                                        <small class="text-muted text-truncate">{{ $instructorName }}</small>
                                    </div>

                                    <p class="course-description small text-muted mb-3 flex-grow-1">
                                        {{ Str::limit($courseDescription, 80) }}
                                    </p>

                                    <div class="course-meta d-flex justify-content-between align-items-center mb-3">
                                        <div class="meta-item d-flex align-items-center">
                                            <i class="fas fa-clock text-primary me-1 small"></i>
                                            <small>{{ $course->duration ?? 0 }}h</small>
                                        </div>
                                        <div class="meta-item d-flex align-items-center">
                                            <i class="fas fa-book text-primary me-1 small"></i>
                                            <small>{{ $course->lessons_count ?? 0 }}</small>
                                        </div>
                                        <div class="meta-item d-flex align-items-center">
                                            <i class="fas fa-graduation-cap text-primary me-1 small"></i>
                                            <small>{{ $course->level ? substr($course->level, 0, 3) : 'All' }}</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="course-price">
                                            @if($course->price > 0)
                                                <span class="text-primary fw-bold">
                                                    {{ $currencySymbol }}{{ number_format($course->price, 0) }}
                                                </span>
                                                @if($course->old_price && $course->old_price > $course->price)
                                                    <del class="text-muted ms-1 small">
                                                        {{ $currencySymbol }}{{ number_format($course->old_price, 0) }}
                                                    </del>
                                                @endif
                                            @else
                                                <span class="text-success fw-bold">{{ __('Free') }}</span>
                                            @endif
                                        </div>
                                        <div class="course-rating d-flex align-items-center">
                                            <i class="fas fa-star text-warning small me-1"></i>
                                            <small>4.5</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                @if($courses->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Courses pagination">
                            <ul class="pagination justify-content-center flex-wrap">
                                {{-- Previous Page Link --}}
                                @if($courses->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ __('Previous') }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->previousPageUrl() }}" rel="prev">{{ __('Previous') }}</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach($courses->getUrlRange(max(1, $courses->currentPage() - 2), min($courses->lastPage(), $courses->currentPage() + 2)) as $page => $url)
                                    @if($page == $courses->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if($courses->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->nextPageUrl() }}" rel="next">{{ __('Next') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ __('Next') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="empty-state py-5">
                            <i class="fas fa-book fa-4x text-muted mb-4"></i>
                            <h3 class="mb-3">{{ __('No Courses Available') }}</h3>
                            <p class="text-muted mb-4">{{ __('We are currently adding new courses. Please check back later.') }}</p>
                            <a href="{{ route('home', ['locale' => $locale]) }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>
                                {{ __('Return Home') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-stats .stat-item {
    padding: 0 1.5rem;
    border-right: 1px solid rgba(255,255,255,0.3);
}

.hero-stats .stat-item:last-child {
    border-right: none;
}

.course-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.course-image-container {
    height: 200px;
}

.course-overlay {
    transition: opacity 0.3s ease;
}

.course-card:hover .course-overlay {
    opacity: 1 !important;
}

.course-title {
    font-size: 1rem;
    line-height: 1.4;
}

.course-description {
    font-size: 0.85rem;
    line-height: 1.5;
}

.meta-item {
    font-size: 0.8rem;
}

.empty-state {
    padding: 3rem 1rem;
}

.section-title {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .hero-stats .stat-item {
        padding: 0 1rem;
    }
}

@media (max-width: 992px) {
    .hero-stats .stat-item {
        padding: 0 0.5rem;
    }

    .course-image-container {
        height: 180px;
    }
}

@media (max-width: 768px) {
    .hero-stats .stat-item {
        padding: 0.5rem 1rem;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.3);
        margin-bottom: 0.5rem;
    }

    .hero-stats .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .language-section .btn-group {
        flex-wrap: wrap;
        justify-content: center;
    }

    .language-section .btn {
        margin: 0.25rem;
    }

    .course-meta {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .course-meta .meta-item {
        margin-right: 1rem;
    }
}

@media (max-width: 576px) {
    .hero-section .display-4 {
        font-size: 2.5rem;
    }

    .course-card {
        margin-bottom: 1.5rem;
    }

    .course-image-container {
        height: 160px;
    }

    .pagination {
        flex-wrap: wrap;
    }

    .page-item {
        margin: 0.25rem;
    }
}
</style>
@endpush
