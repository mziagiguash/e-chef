@extends('backend.layouts.app')
@section('title', 'Course List')

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="content-body">
    <div class="container-fluid">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Course List</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ localeRoute('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('course.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active"><a href="{{ localeRoute('course.index') }}">All Course</a></li>
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

        <div class="card-header mb-3">
            <a href="{{ localeRoute('course.create') }}" class="btn btn-primary">+ Add new</a>
        </div>

        <div class="row">
            @forelse ($course as $d)
                @php
                    $translations = [];
                    foreach($locales as $localeCode => $localeName) {
                        $translations[$localeCode] = [
                            'title' => addslashes($d->translation($localeCode)?->title ?? 'No Title'),
                            'category' => addslashes($d->courseCategory?->getTranslation('category_name', $localeCode) ?? 'No Category'),
                            'instructor' => addslashes($d->instructor?->getTranslation('name', $localeCode) ?? 'No Instructor'),
                        ];
                    }
                @endphp

                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="card card-profile course-card" data-translations="{{ json_encode($translations) }}">
                        <div class="card-header justify-content-end pb-0">
                            <div class="dropdown">
                                <button class="btn btn-link" type="button" data-toggle="dropdown">
                                    <span class="dropdown-dots fs--1"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right border py-0">
                                    <div class="py-2">
                                        <a class="dropdown-item" href="{{ localeRoute('course.edit', encryptor('encrypt', $d->id)) }}">Edit</a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="$('#form{{$d->id}}').submit()">Delete</a>
                                        <form id="form{{$d->id}}" action="{{ localeRoute('course.destroy', encryptor('encrypt', $d->id)) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-2">
                            <div class="text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('uploads/courses/' . $d->image) }}" class="w-100" height="200" alt="">
                                </div>

                                <h3 class="mt-4 mb-1 course-title">{{ $d->translation($currentLocale)?->title ?? 'No Title' }}</h3>

                                <ul class="list-group mb-3 list-group-flush text-left">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Difficulty</span>
                                        <strong>
                                            {{ $d->difficulty == 'beginner' ? __('Beginner') :
                                               ($d->difficulty == 'intermediate' ? __('Intermediate') :
                                               __('Advanced')) }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Instructor</span>
                                        <strong class="course-instructor">{{ $d->instructor?->getTranslation('name', $currentLocale) ?? 'No Instructor' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Category</span>
                                        <strong class="course-category">{{ $d->courseCategory?->getTranslation('category_name', $currentLocale) ?? 'No Category' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Price</span>
                                        <strong>{{ $d->price ? $currentCurrency . number_format($d->price * $currencyRate, 2) : 'Free' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Status</span>
                                        <span class="badge
                                            @if($d->status == 0) badge-warning
                                            @elseif($d->status == 1) badge-danger
                                            @elseif($d->status == 2) badge-success
                                            @endif">
                                            @if($d->status == 0) {{__('Pending')}}
                                            @elseif($d->status == 1) {{__('Inactive')}}
                                            @elseif($d->status == 2) {{__('Active')}}
                                            @endif
                                        </span>
                                    </li>
                                </ul>

                                <a class="btn btn-outline-primary btn-rounded mt-3 px-4" href="about-student.html">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-profile">
                        <div class="card-body pt-2 text-center">
                            <p class="mt-3 px-4">Course Not Found</p>
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

    // Устанавливаем активный таб
    document.querySelectorAll('#courseLangTabs .nav-link').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.locale === savedLocale);
    });

    // Клик по табу
    document.querySelectorAll('#courseLangTabs .nav-link').forEach(tab => {
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
