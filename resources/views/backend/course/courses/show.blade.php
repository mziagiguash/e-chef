@extends('backend.layouts.app')
@section('title', 'Course Details')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course Details</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">Course Details</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $currentLocale = request()->get('lang', app()->getLocale());
            $currentLocale = is_array($currentLocale) ? 'en' : $currentLocale;

            // Получаем переводы для текущего языка
            $currentTranslation = $course->translations->firstWhere('locale', $currentLocale);
            $currentInstructor = $course->instructor ? $course->instructor->translations->firstWhere('locale', $currentLocale) : null;
            $currentCategory = $course->courseCategory ? $course->courseCategory->translations->firstWhere('locale', $currentLocale) : null;
        @endphp

        <div class="row mb-4">
            <div class="col-md-6">
                <ul class="nav nav-tabs" id="courseLangTabs" role="tablist">
                    @foreach ($locales as $localeCode => $localeName)
                        <li class="nav-item" role="presentation">
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $localeCode]) }}"
                               class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}">
                                {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('courses.edit', encryptor('encrypt', $course->id)) }}"
                   class="btn btn-primary btn-sm">Edit Course</a>
                <a href="{{ route('courses.index') }}"
                   class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Course Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center mb-4">
                                    <img src="{{ asset('uploads/courses/' . $course->image) }}"
                                         class="img-fluid rounded"
                                         style="max-height: 300px; object-fit: cover;"
                                         alt="Course Image">
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5>Quick Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Status:</strong>
                                            <span class="badge
                                                @if($course->status == 0) bg-warning
                                                @elseif($course->status == 1) bg-danger
                                                @elseif($course->status == 2) bg-success
                                                @endif">
                                                @if($course->status == 0) Pending
                                                @elseif($course->status == 1) Inactive
                                                @elseif($course->status == 2) Active
                                                @endif
                                            </span>
                                        </p>
                                        <p><strong>Type:</strong> {{ ucfirst($course->courseType) }}</p>
                                        <p><strong>Price:</strong>
                                            @if($course->courseType === 'free')
                                                Free
                                            @elseif($course->courseType === 'paid')
                                                ${{ number_format($course->coursePrice, 2) }}
                                            @elseif($course->courseType === 'subscription')
                                                ${{ number_format($course->subscription_price, 2) }}/month
                                            @endif
                                        </p>
                                        <p><strong>Lessons:</strong> {{ $course->lessons_count }}</p>
                                        <p><strong>Duration:</strong> {{ $course->duration }}</p>
                                        <p><strong>Course Code:</strong> {{ $course->course_code }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5>Course Details - {{ $locales[$currentLocale] }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <h4>{{ $currentTranslation->title ?? 'No Title' }}</h4>
                                        <p class="text-muted">Language: {{ strtoupper($currentLocale) }}</p>

                                        <div class="mb-3">
                                            <strong>Description:</strong>
                                            <p>{!! nl2br(e($currentTranslation->description ?? 'No Description')) !!}</p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Prerequisites:</strong>
                                            <p>{!! nl2br(e($currentTranslation->prerequisites ?? 'No Prerequisites')) !!}</p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Keywords:</strong>
                                            <p>{{ $currentTranslation->keywords ?? 'No Keywords' }}</p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Instructor:</strong>
                                                    {{ $currentInstructor->name ?? ($course->instructor->name ?? 'No Instructor') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Category:</strong>
                                                    {{ $currentCategory->category_name ?? ($course->courseCategory->category_name ?? 'No Category') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Start Date:</strong> {{ $course->formatted_start_from }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Tags:</strong> {{ $course->tag }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Таблица всех переводов -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Автоматическое применение выбранного языка через вкладки
        const langTabs = document.querySelectorAll('.nav-tabs .nav-link');
        langTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        });

        // Подсветка активной вкладки
        const currentLang = '{{ $currentLocale }}';
        document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
            if (tab.href.includes('lang=' + currentLang)) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
    });
</script>
@endsection
