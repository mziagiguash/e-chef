@extends('frontend.layouts.app')
@section('title', $currentTitle ?? 'Course Details')
@section('body-attr') style="background-color: #ebebf2;" @endsection

@section('content')
<!-- Breadcrumb Starts Here -->
<section class="section event-sub-section">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => $locale]) }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>{{ __('Home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.courses', ['locale' => $locale]) }}" class="text-decoration-none">
                            {{ __('Courses') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-truncate" style="max-width: 200px;" title="{{ $currentTitle }}">
                        {{ $currentTitle }}
                    </li>
                </ol>
            </nav>

        <div class="row event-sub-section-main mt-4">
            <div class="col-lg-8">
                <h1 class="font-title--sm mb-3">{{ $currentTitle ?? $course->title }}</h1>

                @if($course->instructor)
                <div class="created-by d-flex align-items-center mb-4">
                    <div class="created-by-image me-3">
                        <img src="{{ asset('uploads/users/'.($course->instructor->image ?? 'default.jpg')) }}"
                             class="rounded-circle" alt="Instructor Image" height="60" width="60" />
                    </div>
                    <div class="created-by-text">
                        <p class="mb-1">Created by</p>
                        <h6 class="mb-0">
                            <a href="{{ localeRoute('frontend.instructor.show', $course->instructor->id) }}">
                                {{ $course->instructor->name ?? 'Unknown Instructor' }}
                            </a>
                        </h6>
                    </div>
                </div>
                @endif

                <!-- Course Thumbnail -->
                <div class="course-overview-image mb-4">
                    <img src="{{ asset('uploads/courses/thumbnails/'.($course->thumbnail_image ?? 'default.jpg')) }}"
                         alt="Course Thumbnail" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;" />
                    @if($course->thumbnail_video)
                    <a class="popup-video play-button" href="{{ $course->thumbnail_video }}">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#1089FF" fill-opacity="0.8"/>
                            <path d="M38 30L26 36V24L38 30Z" fill="white"/>
                        </svg>
                    </a>
                    @endif
                </div>

                <!-- Course Description -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="font-title--card">Course Description</h5>
                        <p class="font-para--lg">{{ $currentDescription ?? $course->description ?? 'No description available' }}</p>
                    </div>
                </div>

                <!-- What You'll Learn -->
                @if(($currentPrerequisites ?? $course->prerequisites) && ($currentPrerequisites ?? $course->prerequisites) != 'No Prerequisites')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="font-title--card">What You'll Learn</h5>
                        <p class="font-para--lg">{{ $currentPrerequisites ?? $course->prerequisites }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Price & Enrollment Section -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <div class="price-section text-center mb-4">
                            @if($course->price > 0)
                                <h3 class="text-primary mb-2">{{ $currencySymbol }}{{ number_format($course->price * ($currencyRate ?? 1), 2) }}</h3>
                                @if($course->old_price > 0 && $course->old_price > $course->price)
                                    <del class="text-muted d-block">{{ $currencySymbol }}{{ number_format($course->old_price * ($currencyRate ?? 1), 2) }}</del>
                                    @php
                                        $discount = (($course->old_price - $course->price) / $course->old_price) * 100;
                                    @endphp
                                    <span class="badge bg-danger mt-1">{{ round($discount) }}% OFF</span>
                                @endif
                            @else
                                <h3 class="text-success">Free</h3>
                            @endif
                        </div>

                        <div class="enrollment-buttons">
                            <a href="{{ route('add.to.cart', $course->id) }}"
                               class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </a>
                            <a href="{{ route('checkout') }}?course_id={{ $course->id }}"
                               class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-credit-card me-2"></i>Buy Now
                            </a>
                        </div>

                        <hr>

                        <div class="course-stats mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-clock text-primary me-2"></i>Duration:</span>
                                <strong>{{ $course->duration ?? 0 }} hours</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-book text-primary me-2"></i>Lessons:</span>
                                <strong>{{ $course->lessons->count() }} lessons</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-users text-primary me-2"></i>Enrolled:</span>
                                <strong>{{ $course->enrolled_count ?? 0 }} students</strong>
                            </div>
                        </div>

                        <div class="course-includes">
                            <h6 class="font-title--xs mb-3">This course includes:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-video text-success me-2"></i>
                                    {{ $course->lessons->count() }} on-demand lessons
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-infinity text-success me-2"></i>
                                    Full lifetime access
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-certificate text-success me-2"></i>
                                    Certificate of completion
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-mobile-alt text-success me-2"></i>
                                    Access on mobile and TV
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Courses -->
@if(isset($relatedCourses) && $relatedCourses->count() > 0)
<section class="section new-course-feature section--bg-offwhite-five">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <h2 class="font-title--md text-center mb-4">Related Courses</h2>
            </div>
        </div>
        <div class="row">
            @foreach($relatedCourses as $relatedCourse)
            <div class="col-md-4 mb-4">
                <div class="contentCard contentCard--course">
                    <div class="contentCard-top">
                        <a href="{{ localeRoute('frontend.courseDetails', $relatedCourse->id) }}">
                            <img src="{{ asset('uploads/courses/'.$relatedCourse->image) }}" alt="images" class="img-fluid" />
                        </a>
                    </div>
                    <div class="contentCard-bottom">
                        <h5>
                            <a href="{{ localeRoute('frontend.courseDetails', $relatedCourse->id) }}" class="font-title--card">
                                {{ $relatedCourse->title }}
                            </a>
                        </h5>
                        <div class="contentCard-info d-flex align-items-center justify-content-between">
                            <span class="price">
                                {{ $relatedCourse->price === null ? 'Free' : $currencySymbol . number_format($relatedCourse->price * $currencyRate, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
.play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

.course-overview-image {
    position: relative;
}

.sticky-top {
    position: sticky;
    z-index: 100;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple video popup functionality
    $('.popup-video').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });
});
</script>
@endpush
