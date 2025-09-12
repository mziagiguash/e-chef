@extends('backend.layouts.app')
@section('title', 'Add Lesson')

@push('styles')
<link href="{{ asset('vendor/summernote/summernote-bs4.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12">
                <h4>Add New Lesson</h4>
                <a href="{{ localeRoute('lesson.index') }}" class="btn btn-secondary mb-3">Back to List</a>
            </div>
        </div>

        @php
            $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
            $appLocale = app()->getLocale();
        @endphp

        <form action="{{ localeRoute('lesson.store') }}" method="POST">
            @csrf

            {{-- Language Tabs --}}
            <ul class="nav nav-tabs mb-3" id="lessonLangTabs" role="tablist">
                @foreach($locales as $localeCode => $localeName)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $localeCode === $appLocale ? 'active' : '' }}"
                           id="tab-{{ $localeCode }}" data-toggle="tab" href="#locale-{{ $localeCode }}" role="tab">
                           {{ $localeName }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Tab Contents --}}
            <div class="tab-content">
                @foreach($locales as $localeCode => $localeName)
                    <div class="tab-pane fade {{ $localeCode === $appLocale ? 'show active' : '' }}" id="locale-{{ $localeCode }}" role="tabpanel">
                        <div class="form-group">
                            <label>Title ({{ $localeName }}) *</label>
                            <input type="text" name="title_{{ $localeCode }}" class="form-control"
                                   value="{{ old('title_'.$localeCode) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description ({{ $localeName }})</label>
                            <textarea name="description_{{ $localeCode }}" class="form-control summernote">{{ old('description_'.$localeCode) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Notes ({{ $localeName }})</label>
                            <textarea name="notes_{{ $localeCode }}" class="form-control summernote">{{ old('notes_'.$localeCode) }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Course Selector --}}
            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->getTitleAttribute() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Lesson</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('.summernote').summernote({height: 150});

    // Переключение табов с повторной инициализацией Summernote
    $('#lessonLangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr('href');
        $(target+' .summernote').summernote({height:150});
    });
});
</script>
@endpush
