@extends('backend.layouts.app')
@section('title', 'Course List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">

        {{-- Заголовок и хлебные крошки --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ localeRoute('course.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">All Courses</li>
                </ol>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $currentLocale = request('lang') ?? app()->getLocale();
        @endphp

        {{-- Таб для выбора языка --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="courseLangTabs" role="tablist">
                    @foreach($locales as $localeCode => $localeName)
                        <li class="nav-item">
                            <a href="#" class="nav-link {{ $localeCode === $currentLocale ? 'active' : '' }}"
                               data-locale="{{ $localeCode }}">
                               {{ $localeName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ localeRoute('course.create') }}" class="btn btn-primary">+ Add New</a>
        </div>

        <div class="row">
            @forelse ($courses as $course)
                @php
                    $translations = [];
                    foreach($locales as $localeCode => $localeName) {
                        $translations[$localeCode] = [
                            'title' => addslashes($course->translation($localeCode)?->title ?? 'No Title'),
                            'category' => addslashes($course->courseCategory?->getTranslation('category_name', $localeCode) ?? 'No Category'),
                            'instructor' => addslashes($course->instructor?->getTranslation('name', $localeCode) ?? 'No Instructor'),
                        ];
                    }
                @endphp

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card course-card" data-translations="{{ json_encode($translations) }}">
                        <div class="card-header d-flex justify-content-end pb-0">
                            <div class="dropdown">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                    <span class="dropdown-dots fs-4">⋮</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-0">
    <div class="py-2">
        <a class="dropdown-item" href="{{ localeRoute('course.edit', encryptor('encrypt', $course->id)) }}">Edit</a>

        <form id="delete-form-{{ $course->id }}" action="{{ localeRoute('course.destroy', encryptor('encrypt', $course->id)) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        <a class="dropdown-item text-danger" href="javascript:void(0);"
   onclick="if(confirm('Are you sure you want to delete this course?')) {
       document.getElementById('delete-form-{{ $course->id }}').submit();
   }">
   Delete
</a>

    </div>
</div>

                            </div>
                        </div>

                        <div class="card-body text-center">
                            <img src="{{ asset('uploads/courses/' . $course->image) }}" class="img-fluid mb-3" style="height:200px; width:100%; object-fit:cover;" alt="Course Image">
                            <h5 class="course-title mb-2">{{ $course->translation($currentLocale)?->title ?? 'No Title' }}</h5>
                            <ul class="list-group list-group-flush text-start">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Instructor</span>
                                    <strong class="course-instructor">{{ $course->instructor?->getTranslation('name', $currentLocale) ?? 'No Instructor' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Category</span>
                                    <strong class="course-category">{{ $course->courseCategory?->getTranslation('category_name', $currentLocale) ?? 'No Category' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Price</span>
                                    <strong>{{ $course->price ? $currentCurrency . number_format($course->price * $currencyRate, 2) : 'Free' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Status</span>
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
                                </li>
                            </ul>
                            <a href="{{ localeRoute('course.show', encryptor('encrypt', $course->id)) }}" class="btn btn-outline-primary btn-sm mt-3">Read More</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <div class="card">
                        <div class="card-body">
                            <p>No courses found.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const savedLocale = localStorage.getItem('courses_lang') || '{{ app()->getLocale() }}';

    function updateCourseLanguage(locale) {
        document.querySelectorAll('.course-card').forEach(card => {
            const translations = JSON.parse(card.dataset.translations);
            card.querySelector('.course-title').textContent = translations[locale].title;
            card.querySelector('.course-instructor').textContent = translations[locale].instructor;
            card.querySelector('.course-category').textContent = translations[locale].category;
        });
    }

    document.querySelectorAll('#courseLangTabs .nav-link').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.locale === savedLocale);
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const locale = this.dataset.locale;
            localStorage.setItem('courses_lang', locale);
            document.querySelectorAll('#courseLangTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            updateCourseLanguage(locale);
        });
    });

    updateCourseLanguage(savedLocale);
});
</script>
@endpush
